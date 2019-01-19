<?php

namespace App\Workflow;

use App\Workflow\Marking\MarkingStoreCollectionInterface as CollectionInterface;

interface PersistStrategyInterface {
    public function isMigrationDisabled(CollectionInterface $store);
    public function isMigrationValid(CollectionInterface $store);
    public function isMigrated(CollectionInterface $store);
    public function getMetadataValue(CollectionInterface $store, string $key);
    public function executeMigration(CollectionInterface $store);
    public function persist(CollectionInterface $store, string $storeId, Marking $marking);
    public function flush();
}
