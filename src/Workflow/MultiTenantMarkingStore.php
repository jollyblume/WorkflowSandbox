<?php

namespace App\Workflow;

use Symfony\Component\Workflow\Marking;
use Ramsey\Uuid\Uuid;

/**
 * MultiTenantMarkingStore
 *
 * MultiTenantMarkingStore is a multi-tenant
 */
class MultiTenantMarkingStore {
    const MARKING_UUID_NAME = 'workflow.marking';
    const STORE_UUID_NAME = 'workflow.marking-store';

    public function getMarking(string $markingStoreId, string $markingId) :Marking {

    }

    public function setMarking(string $markingStoreId, string $markingId, Marking $marking) :void {

    }

    /**
     * Create a new Marking id
     *
     * @return string UUID based on the name, 'workflow.marking'
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function createMarkingId() :string {
        return Uuid::uuid3(Uuid::NAMESPACE_DNS, self::MARKING_UUID_NAME);
    }

    /**
     * Create a new Marking Store id
     *
     * @return string UUID base on the name, 'workflow.marking-store'
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function createMarkingStoreId(string $name) :string {
        return Uuid::uuid3(Uuid::NAMESPACE_DNS, self::STORE_UUID_NAME);
    }
}
