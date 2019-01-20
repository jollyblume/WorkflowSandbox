<?php

namespace JBJ\Common\Collection;

class KeyAwareComposedArrayCollection implements ComposedArrayCollectionInterface
{
    use KeyAwareComposedArrayCollectionTrait;

    public function __construct(array $elements = [], $propertyNames = null, bool $strict = false) {
        $this->setKeyAwarePropertyNames($propertyNames);
        $this->setStrictCollectionMembership($strict);
        if (!empty($elements)) {
            $this->initializeComposedChildren($elements);
        }
    }
}
