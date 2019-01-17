<?php

namespace App\Workflow\Marking;

use App\Collection\ComposedArrayCollectionInterface;
use App\Collection\KeyAwareComposedArrayCollectionTrait;

class MarkingStoreCollection implements ComposedArrayCollectionInterface {
    use KeyAwareComposedArrayCollectionTrait;

    private $markingStoreCollectionId;

    public function __construct(string $markingStoreCollectionId, array $elements = []) {
        $this->markingStoreCollectionId = $markingStoreCollectionId;
        $this->setStrictCollectionMembership(true);
        $this->setKeyAwarePropertyNames('markingStoreId');
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }

    public function getMarkingStoreCollectionId() {
        return $this->markingStoreCollectionId;
    }
}
