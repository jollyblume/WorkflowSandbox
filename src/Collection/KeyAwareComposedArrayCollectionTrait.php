<?php

/**
 * forked from doctrine/collections
 */

namespace App\Collection;

use Closure;
use App\Traits\PropertyAccessorTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

trait KeyAwareComposedArrayCollectionTrait
{
    use ComposedArrayCollectionTrait, PropertyAccessorTrait {
        ComposedArrayCollectionTrait::initializeComposedChildren as baseInitializeComposedChildren;
        ComposedArrayCollectionTrait::set as baseSet;
        ComposedArrayCollectionTrait::add as baseAdd;
    }

    /**
     * Key property name by classname
     *
     * @var array $keyPropertyName
     */
    private $keyPropertyNames;

    protected function setKeyPropertyNames($keyPropertyNames) {
        if (is_array($keyPropertyNames)) {
            // todo validate array
        }

        if (is_string($keyPropertyNames)) {
            $keyPropertyNames = ['__DEFAULT_KEY_PROPERTY_NAME__' => $keyPropertyNames];
        }

        if (!is_array($keyPropertyNames)) {
            // todo exception invalid key property names
        }

        $this->keyPropertyNames = $keyPropertyNames;
    }

    protected function getKeyPropertyName($classname) {
        if (is_object($classname)) {
            $classname = get_class($classname);
        }
        if (!is_string($classname)) {
            return null;
        }
        $keyPropertyNames = $this->keyPropertyNames;
        if (!array_key_exists($classname, $keyPropertyNames)) {
            $classname = '__DEFAULT_KEY_PROPERTY_NAME__';
        }
        return $keyPropertyNames[$classname] ?? null;
    }

    private function getKeyFromElement($element) {
        if (!is_object($element)) {
            return null;
        }
        $keyPropertyName = $this->getKeyPropertyName($element) ?? null;
        $isReadable = $this->isPropertyValueReadable($element, $keyPropertyName);
        $elementKey = $keyPropertyName && $isReadable ?
            $this->getPropertyValue($element, $keyPropertyName) :
            null;
        return $elementKey;
    }

    private function assertExpectedElementKey($providedKey, $element) {
        $elementKey = $this->getKeyFromElement($element) ?? null;
        if ($elementKey && is_string($key) && $elementKey !== $providedKey) {
            // todo exception key's must match
        }
    }

    protected function initializeComposedChildren(array $elements = []) {
        $keyedElements = [];
        foreach ($elements as $key => $value) {
            $this->assertExpectedElementKey($key, $value);
            if (!is_string($key)) {
                $valueKey = $this->getKeyFromElement($value) ?? null;
                $key = $valueKey ?? $key;
            }
            $keyedElements[$key] = $value;
        }

        return static::baseInitializeComposedChildren($keyedElements);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->assertExpectedElementKey($key, $value);
        if (!is_string($key)) {
            $valueKey = $this->getKeyFromElement($value) ?? null;
            $key = $valueKey ?? $key;
        }
        static::baseSet($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function add($element)
    {
        $key = $this->getKeyFromElement($element) ?? null;
        if ($key) {
            static::baseSet($key, $element);
        }
        if (!$key) {
            static::baseAdd($element);
        }
    }
}
