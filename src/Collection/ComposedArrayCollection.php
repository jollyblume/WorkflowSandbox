<?php

namespace App\Collection;

class ComposedArrayCollection implements ComposedArrayCollectionInterface
{
    use ComposedArrayCollectionTrait;

    public function __construct(array $elements = []) {
        if (!empty($elements)) {
            $this->initializeComposedChildren($elements);
        }
    }
}
