<?php

namespace App\Phpcr;

use App\Workflow\Marking\MarkingStoreCollectionInterface;

interface PersistenceStrategyInterface {
    public function isTransitionEnabled(MarkingStoreCollectionInterface $stores)
}
