<?php

/**
 * forked from doctrine/collections
 */

namespace JBJ\Common\Collection;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

trait ComposedArrayCollectionTrait
{
    use ComposedArrayCollectionSharedTrait;

    protected function initializeComposedChildren(array $elements = []) {
        $children = new ArrayCollection($elements);
        $this->children = $children;
        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->getComposedChildren()->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function add($element)
    {
        return $this->getComposedChildren()->add($element);
    }
}
