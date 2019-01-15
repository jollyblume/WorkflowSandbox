<?php

namespace App\Collection;

class ComposedCollection implements ComposedCollectionInterface
{
    use ComposedCollectionTrait;

    public function __construct(array $elements = [], ?string $keyProperty = null) {
        $this->keyProperty = $keyProperty;
        if (!empty($elements)) {
            $this->initializeComposedChildren($elements);
        }
    }
}
