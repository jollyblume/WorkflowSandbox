<?php

namespace App\Workflow;

use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class MarkingStoreMediator implements MarkingStoreInterface {
    /**
     * Workflow ID within the marking store
     *
     * @var string proxyUuid
     */
    private $workflowUuid;

    /**
     * Marking store that serves this marking
     *
     * @var MultiTenantMarkingStore  $markingStore
     */
    private $markingStore;

    public function __construct(string $workflowUuid, MultiTenantMarkingStore $markingStore) {
        $this->workflowUuid = $workflowUuid;
        $this->markingStore = $markingStore;
    }

    public function getWorkflowUuid() :string {
        return $this->workflowUuid;
    }

    public function getMarkingStore() :MultiTenantMarkingStore {
        return $this->markingStore;
    }

    public function getMarking($subject) {
        return $this->markingStore->getMarking($this->workflowUuid, $subject);
    }

    public function setMarking($subject, Marking $marking) {
        $this->markingStore->setMarking($this->workflowUuid, $subject, $marking);
    }
}
