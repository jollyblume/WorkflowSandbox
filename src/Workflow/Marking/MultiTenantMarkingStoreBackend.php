<?php

namespace App\Workflow\Marking;

use Symfony\Component\Workflow\Marking;
use Ramsey\Uuid\Uuid;
use App\Workflow\Marking\MultiTenantMarkingStoreBackendInterface;

/**
 * MultiTenantMarkingStoreBackend
 *
 * MultiTenantMarkingStoreBackend persists the markings for multiple workflows and
 * workflow subjects (tokens).
 */
class MultiTenantMarkingStoreBackend implements MultiTenantMarkingStoreBackendInterface {
    const MARKING_STORE_COLLECTION_NAME = 'workflow.marking-store-collection';
    const MARKING_STORE_NAME = 'workflow.marking-store';

    /**
     * @var MarkingStoreCollection $markingStoreCollection
     */
    private $markingStoreCollection;

    public function __construct(?MarkingStoreCollection $markingStoreCollection = null) {
        if (!$markingStoreCollection) {
            $markingStoreCollectionId = $this->createId(self::MARKING_STORE_COLLECTION_NAME);
            $markingStoreCollection = new MarkingStoreCollection($markingStoreCollectionId);
        }
        $this->markingStoreCollection = $markingStoreCollection;
    }

    protected function getMarkingStoreCollection() {
        return $this->markingStoreCollection;
    }

    /**
     * Get a workflow marking from the backend
     *
     * @param string $markingStoreId
     * @param string $markingId
     * @return Marking The workflow marking
     */
    public function getMarking(string $markingStoreId, string $markingId) {
        $stores = $this->getMarkingStoreCollection();
        $store = $stores[$markingStoreId] ?? null;
        if (!$store) {
            return null;
        }

        $marking = $store[$markingId] ?? null;
        return $marking;
    }

    /**
     * Persist a workflow marking to the backend
     *
     * @param string $markingStoreId
     * @param string $markingId
     * @param Marking $marking The workflow marking
     * @return self
     */
    public function setMarking(string $markingStoreId, Marking $marking) {
        $stores = $this->getMarkingStoreCollection();
        $store = $stores[$markingStoreId] ?? null;
        if (!$store) {
            $store = new MarkingCollection($markingStoreId);
            $stores[] = $store;
        }
        $store[] = $marking;
        return $this;
    }

    /**
     * Create a new Marking id
     *
     * The markingId can be any string that is unique within a workflow domain,
     * but is normally a UUID.
     *
     * Some important workflow domains:
     *  - subject id
     *  - workflow/marking-store id
     *  - workflow/marking-store collection id
     *  - workflow definition
     *
     * @param string $name Name used in UUID5 generation
     * @return string UUID string
     * @throws \Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function createId(string $name = 'workflow.general') :string {
        return Uuid::uuid3(Uuid::NAMESPACE_DNS, $name);
    }
}
