<?php

namespace App\Workflow\Marking;

use App\Collection\ComposedArrayCollectionInterface;
use App\Collection\KeyAwareComposedArrayCollectionTrait;

class MarkingStoreCollection implements ComposedArrayCollectionInterface {
    use KeyAwareComposedArrayCollectionTrait;

    private $markingStoreRegistryId;

    public function __construct(string $markingStoreRegistryId, array $elements = []) {
        $this->strictCollectionMembership = true;
        $this->markingStoreRegistryId = $markingStoreRegistryId;
        $this->setKeyPropertyNames('markingStoreId');
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }

    public function getMarkingStoreRegistryId() {
        return $this->markingStoreRegistryId;
    }
}
