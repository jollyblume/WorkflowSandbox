<?php

namespace App\Collection;

class KeyAwareComposedArrayCollection implements ComposedArrayCollectionInterface
{
    use KeyAwareComposedArrayCollectionTrait;

    public function __construct($keyPropertyNames, array $elements = [], bool $strictCollectionMembership = false) {
        $this->strictCollectionMembership = $strictCollectionMembership;
        $this->setKeyPropertyNames($keyPropertyNames);
        if (!empty($elements)) {
            $this->initializeComposedChildren($elements);
        }
    }
}
