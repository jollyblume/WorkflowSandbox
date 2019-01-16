<?php

namespace App\Workflow\Marking;

use App\Collection\ComposedArrayCollectionInterface;
use App\Collection\KeyAwareComposedArrayCollectionTrait;

class MarkingCollection implements ComposedArrayCollectionInterface {
    use KeyAwareComposedArrayCollectionTrait;

    private $markingId;

    public function __construct(string $markingId, array $elements = []) {
        $this->markingId = $markingId;
        $this->setKeyPropertyNames('markingId');
        if ($elements) {
            $this->initializeComposedChildren($elements);
        }
    }

    public function getMarkingId() {
        return $this->markingId;
    }
}
