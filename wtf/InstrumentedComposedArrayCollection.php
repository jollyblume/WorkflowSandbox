<?php

namespace App\Collection;

class InstrumentedComposedArrayCollection implements ComposedArrayCollectionInterface
{
    use InstrumentedComposedArrayCollectionTrait;

    public function __construct(array $elements = [], ?string $autokeyPropertyName = null) {
        $this->autokeyPropertyName = $autokeyPropertyName;
        if (!empty($elements)) {
            $this->initializeComposedChildren($elements);
        }
    }
}
