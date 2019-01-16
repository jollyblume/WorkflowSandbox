<?php

namespace App\Collection;

class KeyAwareComposedArrayCollection implements ComposedArrayCollectionInterface
{
    use KeyAwareComposedArrayCollectionTrait;

    public function __construct(array $elements = []) {
        if (!empty($elements)) {
            $this->initializeComposedChildren($elements);
        }
    }
}
