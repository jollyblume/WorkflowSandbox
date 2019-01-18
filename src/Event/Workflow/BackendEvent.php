<?php

namespace App\Event\Workflow;

use App\Workflow\Marking;
use App\Workflow\Marking\MarkingStoreCollection;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

class BackendEvent extends BaseEvent {
    private $markingStoreId;
    private $marking;
    private $stores
    public function __construct(string $markingStoreId, Marking $marking, MarkingStoreCollection $stores) {
        $this->markingStoreId = $markingStoreId;
        $this->marking = $marking;
        $this->stores = $stores;
    }
    public function getMarkingStoreId() {
        return $this->markingStoreId;
    }
    public function getMarking() {
        return $this->marking;
    }
    public function getStores() {
        return $this->stores;
    }
}
