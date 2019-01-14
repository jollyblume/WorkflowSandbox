<?php

namespace App\Workflow\Marking;

/*
 * Forked from symfony/workflow
 */

namespace App\Workflow;

use Symfony\Component\Workflow\Marking as BaseMarking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use App\Workflow\MultiTenantMarkingStoreBackend;

/**
 * MultiTenantMarkingStore
 *
 * MultiTenantMarkingStore is forked from the symfony/workflow component and implements
 * the component's MarkingStoreInterface.
 *
 * This marking store maintains the marking for every subject participating in
 * this workflow. Marking stores are persisted to a MultiTenantMarkingStoreBackend.
 *
 * Each MarkingStore instance has a UUID, called 'markingStoreId'. This is used
 * to uniquely identify the marking store and it's related workflow.
 *
 * Each subject (token) participating in any workflow will be injected with a
 * UUID, called 'markingId'. This will only be injected once and will uniquely
 * identify this subject (token) throughout the marking store backend.
 */
class MultiTenantMarkingStore implements MarkingStoreInterface {
    use \App\Traits\PropertyAccessorTrait;

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
     * @var MultiTenantMarkingStoreBackend $backend
     */
    private $backend;

    public function __construct(MultiTenantMarkingStoreBackend $backend) {
        $this->backend = $backend;
        $this->markingStoreId = $backend->createMarkingStoreId();
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getMarkingStoreId() :string {
        return $this->markingStoreId;
    }

    public function getMarkingStoreBackend() :MultiTenantMarkingStoreBackend {
        return $this->backend;
    }

    public function getMarkingId($subject) {
        return $this->getPropertyValue($subject, self::MARKING_ID_PROPERTY);
    }

    public function getMarking($subject) {
        $markingId = $this->getMarkingId($subject);
        if (!$markingId) {
            return new BaseMarking();
        }

        return $this->backend->getMarking($this->markingStoreId, $markingId);
    }

    public function setMarking($subject, BaseMarking $marking) {
        if (!$marking instanceof Marking) {
            $places = $marking->getPlaces();
            // todo validate $subject is a UUID?
            $subject = $this->backend->createMarkingId();
            $marking = new Marking($subject, $places);
        }

        $markingId = $marking->getMarkingId();
        $this->backend->setMarking($this->markingStoreId, $markingId, $marking);
        return $this;
    }
}
