<?php

namespace App\Workflow\Marking;

use App\Collection\ComposedArrayCollectionInterface;

class MarkingStoreCollection implements ComposedArrayCollectionInterface {
    use \App\Collection\ComposedArrayCollectionTrait;

    public function __construct(array $elements = []) {
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }
}
