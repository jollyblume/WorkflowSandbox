<?php

namespace App\Workflow\Persist;

use App\Workflow\PersistStrategyInterface;
use App\Workflow\Marking\MarkingStoreCollectionInterface as StoreCollectionInterface;
use App\Workflow\Marking\MarkingStoreCollection as StoreCollection;
use App\Workflow\Marking\MarkingCollection as MarkingCollection;
use App\Workflow\Marking;
use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;

class PhpcrPersistStrategy implements PersistStrategyInterface {
    /**
     * @var ManagerRegistry $registry
     */
    private $registry;

    /**
     * $migrationMap = [
     *      'class-name' => [<migration-metadata>],
     *      'class-name' => '__DISABLED__', // blacklisted
     *      'class-name' => '__FINAL__', // all ready migrated
     * ];
     *
     * @var array $migrationMap
     */
    private $migrationMap;

    public function __construct(ManagerRegistry $registry, array $migrationMap = []) {
        $this->registry = $registry;
        $this->migrationMap = $migrationMap;
    }

    protected function getClassFromSubject($subject) {
        if (is_object($subject)) {
            $subject = get_class($subject);
        }
        if (!class_exists($subject)) {
            throw new \Exception('class not exists');
        }
        return $subject;
    }

    protected getMetadata(StoreCollectionInterface $store) {
        $subject = $this->getClassFromSubject($store);
        $migrationMap = $this->migrationMap;
        if (!array_key_exists($subject, $migrationMap)) {
            return [];
        }
        $metadata = $migrationMap[$subject] ?? null;
        return $metadata;
    }

    public function getMetadataValue(StoreCollectionInterface $store, string $key) {
        $metadata = $this->getMetadata($store);
        if (is_string($metadata) || !array_key_exists($key, $metadata)) {
            return null;
        }
        $value = $metadata[$key] ?? null;
        return $value;
    }

    public function isMigrationDisabled(StoreCollectionInterface $store) {
        $isDisabled =
            '__DISABLED__' === $this->getMetadata($store) ||
            true === boolval($this->getMetadataValue('__DISABLED__'));
        return $isDisabled;
    }

    public function isMigrationValid(StoreCollectionInterface $store) {
        if($this->isDisabled($store)) {
            return false;
        }
        $isValid = true === boolval($this->getMetadataValue('__IS_VALID__'));
        return $isValid;
    }

    public function isMigrated(StoreCollectionInterface $store) {
        $isFinal = '__FINAL__' === $this->getMetadata($store);
        return $isFinal;
    }

    public function hasMigrationPath(StoreCollectionInterface $store) {
        $hasPath =
            $this->isMigrationValid($store) &&
            !$this->isMigrated($store);
        return $hasPath;
    }

    public function executeMigrationPath(StoreCollectionInterface $originalStore) {
        if (!$this->hasMigrationPath($originalStore)) {
            throw new \Exception('no execution path found');
        }
        $storesId = $originalStore->getMarkingStoreCollectionId();
        $storeElements = $originalStore->toArray();
        $stores = new StoreCollection($storesId, $storeElements);
        foreach ($stores as $markingsId => $originalMarkings) {
            $markingsId = $originalMarkings->getMarkingCollectionId();
            $markingElements = $originalMarkings->toArray();
            $markings = new MarkingCollection($markingsId, $markingElements);
            foreach ($markings as $markingId => $marking) {
                $subjectId = $marking->getMarkingId();
                $placeElements = $marking->getPlaces();
                $marking = new Marking($subjectId, $placeElements);
            }
        }
        return $stores;
    }

    public function persist(StoreCollectionInterface $store, string $markingStoreId, Marking $marking) {

    }

    public function flush() {

    }
}
