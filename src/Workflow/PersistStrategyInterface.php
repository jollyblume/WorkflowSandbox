<?php

namespace App\Workflow;

use App\Workflow\Marking\MarkingStoreCollectionInterface;

interface PersistStrategyInterface {
    public function isMigrated(MarkingStoreCollectionInterface $stores);
    public function hasMigrationPath(MarkingStoreCollectionInterface $stores);
    public function executeMigrationPath(MarkingStoreCollectionInterface $stores);
    public function persist(MarkingStoreCollectionInterface $stores, string $markingStoreId, Marking $marking);
    public function flush();
}
