<?php

/*
 * Forked from symfony/workflow
 */

namespace App\Workflow;

use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use App\Workflow\MultiTenantMarkingStore;

/**
 * MarkingStore
 *
 * MarkingStore is forked from the symfony/workflow component and implements
 * the component's MarkingStoreInterface.
 *
 * This marking store maintains the marking for every subject participating in
 * this workflow. Marking stores are persisted to a MultiTenantMarkingStore.
 *
 * Each MarkingStore instance has a UUID, called 'markingStoreId'. This is used
 * to uniquely identify the marking store and it's related workflow.
 *
 * Each subject (token) participating in any workflow will be injected with a
 * UUID, called 'markingId'. This will only be injected once and will uniquely
 * identify this subject (token) throughout the marking store backend.
 */
class MarkingStore implements MarkingStoreInterface {
    const MARKING_ID_PROPERTY = 'markingId';

    /**
     * Marking Store ID
     *
     * @var string $markingStoreId
     */
    private $markingStoreId;

    /**
     * Marking store backend
     *
     * @var MultiTenantMarkingStore $backend
     */
    private $backend;

    /**
     * @var PropertyAccessorInterface $propertyAccessor
     */
    private $propertyAccessor;

    public function __construct(MultiTenantMarkingStore $backend) {
        $this->backend = $backend;
        $this->markingStoreId = $backend->createMarkingStoreId();
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getMarkingStoreId() :string {
        return $this->markingStoreId;
    }

    public function getMarkingStoreBackend() :MultiTenantMarkingStore {
        return $this->backend;
    }

    public function getMarkingId($subject) {
        return $this->getPropertyValue($subject, self::MARKING_ID_PROPERTY);
    }

    public function getMarking($subject) {
        $markingId = $this->getMarkingId($subject);
        if (!$markingId) {
            return new Marking();
        }

        return $this->backend->getMarking($this->markingStoreId, $markingId);
    }

    public function setMarking($subject, Marking $marking) {
        $markingId = $this->getMarkingId($subject);
        if (!$markingId) {
            $markingId = $this->backend->createMarkingId();
        }

        $this->backend->setMarking($this->markingStoreId, $markingId, $marking);
    }

    private function getPropertyValue($subject, string $property) {
        $this->propertyAccessor->getValue($subject, $property);
    }

    private function setPropertyValue($subject, string $property, $value) {
        $this->propertyAccessor->setValue($subject, $property, $value);
    }
}
