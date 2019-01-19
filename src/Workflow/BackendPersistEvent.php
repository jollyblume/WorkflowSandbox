<?php

namespace App\Workflow;

use App\Workflow\Marking\MarkingStoreCollectionInterface;

class BackendPersistEvent extends BackendEvent {
    public function setStores(MarkingStoreCollectionInterface $stores) {
        $this>stores = $stores;
    }
}
