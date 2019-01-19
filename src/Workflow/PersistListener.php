<?php

namespace App\Workflow;

use Psr\Log\LoggerInterface;
use App\Workflow\Marking\MarkingStoreCollectionInterface;
use App\Workflow\BackEndPersistEvent as Event;
use App\Traits\PropertyAccessorTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistListener implements EventSubscriberInterface
{
    use PropertyAccessorTrait;

    const 'STATUS_PROPERTY_NAME' = 'persistenceStatus';
    const 'STATUS_DISABLED' = '__DISABLED__';
    const 'STATUS_PERSISTABLE' = '__PERSISTABLE__';
    const 'STATUS_MANAGED' = '__MANAGED__';
    const 'STATUS_UNKNOWN' = '__UNKNOWN__';

    private $statusLogged = false;
    private $logger;
    private $strategy;

    public function __construct(LoggerInterface $logger, PersistStrategyInterface $strategy = null)
    {
        $this->logger = $logger;
        $this->strategy = $strategy;
    }

    protected function isPersistenceStatusReadable(Event $event) {
        $stores = $event->getStores();
        $isReadable = $this->isPropertyValueReadable($stores, self::TRANSITION_STATUS_PROPERTY);
        return $isReadable;
    }

    protected function getPersistenceStatus(Event $event) {
        $isReadable = $this->isPersistenceStatusReadable($event);
        if (!$isReadable) {
            return self::STATUS_DISABLED;
        }
        $stores = $event->getStores();
        $status = $this->getPropertyValue($stores, self::STATUS_PROPERTY_NAME);
        foreach ([self::STATUS_DISABLED, self::STATUS_PERSISTABLE, self::STATUS_MANAGED] as $allowed) {
            if ($allowed === $status) {
                return $status;
            }
        }
        return self::STATUS_UNKNOWN;
    }

    protected function setPersistenceStatus(Event $event, string $status) {
        $isReadable = $this->isPersistenceStatusReadable($stores);
        if (!$isReadable) {
            throw new \Exception('not readable');
        }
        foreach ([self::STATUS_DISABLED, self::STATUS_PERSISTABLE, self::STATUS_MANAGED] as $allowed) {
            if ($allowed === $status) {
                $stores = $event->getStores();
                $this->setPropertyValue($stores, self::TRANSITION_STATUS_PROPERTY, $status);
                return $this;
            }
        }
        throw new \Exception('invalid status');
    }

    protected function isMigrated(Event $event) {
        $stores = $event->getStores();
        return $this->strategy->isMigrated($stores);
    }

    protected function hasMigrationPath(Event $event) {
        $stores = $event->getStores();
        return $this->strategy->hasMigrationPath($stores);
    }

    protected function executeMigrationPath(Event $event) {
        $status = $this->getPersistenceStatus($event);
        if ($status !== self::STATUS_PERSISTABLE) {
            throw new \Exception('must be persistable to migrate');
        }
        $stores = $event->getStores();
        $migratedStores = $this->strategy->executeMigrationPath($stores);
        if ($stores !== $migratedStores) {
            $event->setStores($migratedStores);
            $markingStoreCollectionId = $event->getMarkingStoreCollectionId();
            $this->logger->info(sprintf('Marking store "%s" migrated', $markingStoreCollectionId));
        }
        $this->setPersistenceStatus($event, self::STATUS_MANAGED);
        return $migratedStores;
    }

    protected function handleStatusUnknown(Event $event) {
        $hasMigrationPath = $this->hasMigrationPath($event);
        $this->setPersistenceStatus($event, self::STATUS_PERSISTABLE);

    }

    public function onPersist(Event $event) {
        $status = $this->getPersistenceStatus($event);
        if ($status === self::STATUS_DISABLED) {
            $markingStoreCollectionId = $event->getMarkingStoreCollectionId();
            $this->logger->warn(sprintf('Marking store "%s" disabled', $markingStoreCollectionId));
            return;
        }
        if ($status === self::STATUS_PERSISTABLE) {
            throw new \Exception('logic exception: should never start onPersist in this status');
        }
        $isMigrated = $this->isMigrated($event);
        if ($status !== self::STATUS_MANAGED && $isMigrated) {
            //todo log unmanged, but migrated stores set to managed
            $this->setPersistenceStatus($event, self::STATUS_MANAGED);
            $status = self::STATUS_MANAGED;
        }
        if ($status !== self::STATUS_MANAGED) {
            $status = $this->handleStatusUnknown($event);
        }
        if ($status !== self::STATUS_MANAGED) {
            $this->setPersistenceStatus($event, self::STATUS_DISABLED);
            $markingStoreCollectionId = $event->getMarkingStoreCollectionId();
            $this->logger->warn(sprintf('Marking store "%s" disabled: unknown reason', $markingStoreCollectionId));
            return;
        }
        $stores = $event->getStores();
        $markingStoreId = $event->getMarkingStoreId();
        $marking = $event->getMarking();
        $strategy = $this->strategy;
        if (!$strategy) {
            $this->logger->error('No persist strategy defined.');
            return;
        }
        $strategy->persist($stores, $markingStoreId, $marking);
    }

    public static function getSubscribedEvents()
    {
        return [
            'backend.mark.persist' => ['onPersist'],
        ];
    }
}
