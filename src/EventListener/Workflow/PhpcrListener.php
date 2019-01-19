<?php

namespace App\EventListener\Workflow;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Workflow\Marking\MarkingStoreCollectionInterface;
use App\Event\Workflow\PersistEvent as Event;
use App\Phpcr\PersistenceStrategyInterface;
use App\Traits\PropertyAccessorTrait;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class PhpcrListener implements EventSubscriberInterface
{
    use PropertyAccessorTrait;

    const 'TRANSITION_STATUS_PROPERTY' = 'transitionStatus';
    const 'STATUS_PERSISTABLE' = '__PERSISTABLE__';
    const 'STATUS_MANAGED' = '__MANAGED__';
    const 'STATUS_DISABLED' = '__MANAGED__';

    private $enabled;
    private $statusLogged = false;
    private $logger;
    private $registry;
    private $strategy;

    public function __construct(LoggerInterface $logger, ManagerRegistry $registry, PersistenceStrategyInterface $strategy = null, bool $enabled = true)
    {
        $this->logger = $logger;
        $this->registry = $registry;
        $this->strategy = $strategy;
        $this->enabled = boolval($enabled);
    }

    public function isEnabled() {
        $enabled = $this->enabled;
        return boolval($enabled)
    }

    protected function disable(string $msg = '') {
        $this->enabled = false;
        $this->logger->warn(sprintf('Disabling persistence: %s.', $msg));
    }

    protected function getTransitionStatus(MarkingStoreCollectionInterface $stores) {
        $status = $this->getPropertyValue($stores, self::TRANSITION_STATUS_PROPERTY, $status);
        return $status;
    }

    protected function setTransitionStatus(MarkingStoreCollectionInterface $stores, string $status) {
        $this->setPropertyValue($stores, self::TRANSITION_STATUS_PROPERTY, $status);
        return $this;
    }

    protected function isTransitionEnabled(MarkingStoreCollectionInterface $stores) {
        $status = $this->getTransitionStatus($stores);
        if (self::STATUS_PERSISTABLE !== $status) {
            return false;
        }
        $enabled = $this->strategy->isTransitionEnabled($stores);
        if (!$enabled) {
            $this->setTransitionStatus($stores, self::STATUS_DISABLED);
        }
        return $enabled;
    }

    protected function isStoresPersistable(MarkingStoreCollectionInterface $stores) {
        $markingStoreCollectionId = $stores->getMarkingStoreCollectionId();
        $isReadable = $this->isPropertyValueReadable($stores, self::TRANSITION_STATUS_PROPERTY);
        if (!$isReadable) {
            $this->logger->error(sprintf('Marking store collection %s not persistable.', $markingStoreCollectionId));
            return false;
        }
        $status = $this->getTransitionStatus($stores);
        if (self::STATUS_DISABLED === $status) {
            $this->logger->error(sprintf('Marking store collection %s is disabled.', $markingStoreCollectionId));
            return false;
        }
        if (self::STATUS_MANAGED === $status) {
            return true;
        }
        $this->setTransitionStatus($stores, self::STATUS_PERSISTABLE);
        $enabled = $this->isTransitionEnabled($stores);
        return true;
    }

    protected function transitionStores(MarkingStoreCollectionInterface $stores) {
        $transitionedStores = $this->strategy->transitionStores($stores);
        if ($stores !== $transitionedStores) {
            $markingStoreCollectionId = $stores->getMarkingStoreCollectionId();
            $this->logger->info(sprintf('Marking store ', $markingStoreCollectionId));
        }
        return $transitionedStores;
    }

    protected function persistStores(MarkingStoreCollectionInterface $stores) {
        //todo create persist strategy to persist store
        throw new \Exception('not imlemented');
    }

    // todo develop class transition   strategy
    public function onPersist(Event $event)
    {
        $enabled = $this->enabled;
        $statusLogged = $this->statusLogged;
        if (!$enabled && !$statusLogged) {
            $this->logger->warn('Exiting "onPersist", persistence disabled.');
            $this->statusLogged = true;
        }
        if (!$enabled) {
            return;
        }

        $this->logger->info(sprintf('Persisting marking "%s" to store "%s".'), $markingId, $storeId);
        $stores = $event->getStores();
        $isPersistable = $this->isStoresPersistable($stores);
        if (!$isPersistable )
        {
            return;
        }
        $status = $this->getTransitionStatus($stores);
        if (self::STATUS_PERSISTABLE === $status) {
            $transitionedStores = $this->transitionStores($stores);
            $event->setStores($transitionedStores);
            $this->setPropertyValue($managedStores, self::TRANSITION_STATUS_PROPERTY, self::STATUS_MANAGED);
        }
        if (self::STATUS_MANAGED === $status) {
            $this->persistStores($stores);
        }
        $this->logger->info(sprintf('Marking "%s" persisted to store "%s".'), $markingId, $storeId);
    }

    public static function getSubscribedEvents()
    {
        return [
            'backend.mark.persist' => ['onPersist'],
        ];
    }
}
