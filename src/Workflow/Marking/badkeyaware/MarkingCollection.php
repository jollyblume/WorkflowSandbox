<?php

namespace App\Workflow\Marking;

use App\Collection\ComposedArrayCollectionInterface;
use App\Collection\KeyAwareComposedArrayCollectionTrait;

class MarkingCollection implements ComposedArrayCollectionInterface {
    use KeyAwareComposedArrayCollectionTrait;

    private $markingStoreId;

    public function __construct(string $markingStoreId, array $elements = []) {
        $this->strictCollectionMembership = true;
        $this->markingStoreId = $markingStoreId;
        $this->setKeyPropertyNames('markingId');
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }

    public function getMarkingStoreId() {
        return $this->markingStoreId;
    }
}