<?php

namespace App\Workflow\Marking;

use App\Collection\ComposedArrayCollectionInterface;
use App\Collection\KeyAwareComposedArrayCollectionTrait;

class MarkingStoreCollection implements ComposedArrayCollectionInterface {
    use KeyAwareComposedArrayCollectionTrait;

    private $markingStoreCollectionId;

    public function __construct(string $markingStoreCollectionId, array $elements = []) {
        $this->strictCollectionMembership = true;
        $this->markingStoreCollectionId = $markingStoreCollectionId;
        $this->setKeyPropertyNames('markingStoreId');
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }

    public function getMarkingStoreCollectionId() {
        return $this->markingStoreCollectionId;
    }
}
