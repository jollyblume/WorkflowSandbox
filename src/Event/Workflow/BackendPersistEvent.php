<?php

namespace App\Event\Workflow;

class BackendPersistEvent extends BackendEvent {
    public function setStores(MarkingStoreCollection $stores) {
        $this>stores = $stores;
    }
}
