<?php

/**
 * forked from doctrine/collections
 */

namespace App\Collection;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use App\Traits\PropertyAccessorTrait;
use App\Exception\OutOfScopeException;

trait KeyAwareComposedArrayCollectionTrait
{
    use ComposedArrayCollectionSharedTrait, PropertyAccessorTrait;

    /**
     * @var array $keyAwarePropertyNames
     */
    private $keyAwarePropertyNames = [];

    /**
     * @var bool $strictCollectionMembership
     */
    private $strictCollectionMembership = false;

    public function isStrictCollectionMembership() {
        $strictCollectionMembership = $this->strictCollectionMembership;
        return boolval($strictCollectionMembership);
    }

    private function setStrictCollectionMembership(bool $strict = false) {
        $this->strictCollectionMembership = $strict;
    }

    private function setKeyAwarePropertyNames($propertyNames) {
        if (!$propertyNames) {
            $propertyNames = [];
        }
        if (is_array($propertyNames)) {
            $indexedCount = 0;
            $hasDefault = false;
            foreach ($propertyNames as $key => $value) {
                if (is_numeric($key)) {
                    $indexedCount++;
                }
                if ('__DEFAULT_PROPERTY_NAME__' === $key) {
                    $hasDefault = true;
                }
                if (!is_string($value)) {
                    throw new \Exception();
                }
            }
            if (1 < $indexedCount) {
                throw new \Exception('only 1 default key allowed');
            }
            if (1 === $indexedCount && $hasDefault) {
                throw new \Exception('only 1 of: indexed default or specified default');
            }
            if (1 === $indexedCount) {
                $defaultName = $propertyNames[0];
                unset($propertyNames[0]);
                $propertyNames['__DEFAULT_PROPERTY_NAME__'] = $defaultName;
            }
        }
        if (is_string($propertyNames)) {
            $propertyNames = [
                '__DEFAULT_PROPERTY_NAME__' => $propertyNames,
            ];
        }
        $this->keyAwarePropertyNames = $propertyNames;
    }

    public function getKeyAwarePropertyNames() {
        $keyAwarePropertyNames = $this->keyAwarePropertyNames;
        return $keyAwarePropertyNames;
    }

    public function getKeyAwarePropertyNameDefault() {
        $propertyName = $this->getKeyAwarePropertyName('__DEFAULT_PROPERTY_NAME__');
        return $propertyName ?? null;
    }

    public function hasKeyAwarePropertyNameDefault() {
        $default = $this->getKeyAwarePropertyNameDefault();
        return is_string($default);
    }

    public function getKeyAwarePropertyName($element) {
        $propertyNames = $this->keyAwarePropertyNames;
        if (is_object($element)) {
            $element = get_class($element);
        }
        if (!is_string($element)) {
            return null;
        }
        if (!array_key_exists($element, $propertyNames)) {
            $element = '__DEFAULT_PROPERTY_NAME__';
        }
        if (array_key_exists($element, $propertyNames)) {
            $propertyName = $propertyNames[$element];
            return $propertyName;
        }
        return null;
    }

    public function hasKeyAwarePropertyNames() {
        $propertyNames = $this->getKeyAwarePropertyNames();
        return empty(!$propertyNames);
    }

    protected function isKeyAwarePropertyReadable(?string $propertyName, $element) {
        if (is_object($element) && $propertyName) {
            return $this->isPropertyValueReadable($element, $propertyName);
        }
        return false;
    }

    protected function getKeyAwarePropertyValue($element) {
        $propertyName = $this->getKeyAwarePropertyName($element);
        if ($this->isKeyAwarePropertyReadable($propertyName, $element)) {
            $propertyValue = $this->getPropertyValue($element, $propertyName);
            return $propertyValue;
        }
        return null;
    }

    private function assertElementAllowedInCollection($element) {
        $propertyName = $this->getKeyAwarePropertyName($element);
        if ($propertyName && !$this->isKeyAwarePropertyReadable($propertyName, $element)) {
            $propertyName = null;
        }
        if (!$propertyName && $this->isStrictCollectionMembership()) {
            $contextParameter = [
                'element' => $element,
            ];
            throw new OutOfScopeException($contextParameter);
        }
        if ($propertyName && $this->isStrictCollectionMembership()) {
            $propertyValue = $this->getKeyAwarePropertyValue($element);
            if (!is_string($propertyValue) || empty($propertyValue)) {
                throw new \Exception();
            }
        }
    }

    private function assertKeyAllowedInCollection($key, $element) {
        if (!is_string($key) || !is_object($element)) {
            return;
        }
        $propertyValue = $this->getKeyAwarePropertyValue($element);
        if (!$propertyValue) {
            return;
        }
        if ($key !== $propertyValue) {
            $contextParameter = [
                'key' => $key,
                'element' => $element,
                'debug' => 'try setting $key = null',
            ];
            throw new OutOfScopeException($contextParameter);
        }
    }

    protected function initializeComposedChildren(array $elements = []) {
        if ($this->hasKeyAwarePropertyNames() || $this->isStrictCollectionMembership()) {
            $keyedElements = [];
            foreach ($elements as $key => $value) {
                $this->assertElementAllowedInCollection($value);
                $this->assertKeyAllowedInCollection($key, $value);
                if ($key) {
                    $keyedElements[$key] = $value;
                }
                if (!$key) {
                    $keyedElements[] = $value;
                }
            }
            $elements = $keyedElements;
        }
        $children = new ArrayCollection($elements);
        $this->children = $children;
        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->assertElementAllowedInCollection($value);
        $this->assertKeyAllowedInCollection($key, $value);
        if (!is_string($key)) {
            $propertyValue = $this->getKeyAwarePropertyValue($value) ?? null;
            $key = $propertyValue ?? $key;
        }
        $this->getComposedChildren()->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function add($element)
    {
        $this->assertElementAllowedInCollection($element);
        $propertyValue = $this->getKeyAwarePropertyValue($element);
        if ($propertyValue) {
            $this->set(null, $element);
        }
        if (!$propertyValue) {
            $this->getComposedChildren()->add($element);
        }
    }
}
