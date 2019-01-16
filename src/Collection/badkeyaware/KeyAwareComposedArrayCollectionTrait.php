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
    use PropertyAccessorTrait;

    /**
     * Key property name by classname
     *
     * @var array $keyPropertyName
     */
    private $keyPropertyNames;

    /**
     * Classes included in $keyPropertyNames are the only collection members.
     *
     * An exception is thrown when attempting to add a non-member
     *
     * @var bool $strictCollectionMembership
     */
    private $strictCollectionMembership = false;

    /**
     * composedCollection
     *
     * @var ArrayCollection $children
     */
    private $children;

    public function isStrictCollectionMembership() {
        return boolval($this->strictCollectionMembership);
    }

    public function isCollectionMember($element) {
        $keyPropertyName = $this->getKeyPropertyName($element);
        if (null !== $keyPropertyName) {
            $isReadable = $this->isPropertyValueReadable($element, $keyPropertyName);
            if ($isReadable) {
                return true;
            }
        }
        return false;
    }

    private function assertElementIsAcceptable($element) {
        if ($this->isStrictCollectionMembership() && !$this->isCollectionMember($element)) {
            $contextParameters = [
                'element' => $element,
            ];

            throw new OutOfScopeException($contextParameters);
        }
    }

    protected function setKeyPropertyNames($keyPropertyNames) {
        if (is_array($keyPropertyNames)) {
            $indexedCount = 0;
            foreach ($keyPropertyNames as $key => $value) {
                if (is_numeric($key)) {
                    $indexedCount++;
                }
                if (is_string($key) && !class_exists($key)) {
                    // todo exception
                }
                if (!is_string($value)) {
                    // todo exception
                }
            }
            if (1 < $indexedCount) {
                // todo exception only 1 default allowed
            }
            if ( 1 === $indexedCount) {
                $defaultName = $keyPropertyNames[0];
                unset($keyPropertyNames[0]);
                $keyPropertyNames['__DEFAULT_KEY_PROPERTY_NAME__'] = $defaultName;
            }
        }

        if (is_string($keyPropertyNames)) {
            $keyPropertyNames = ['__DEFAULT_KEY_PROPERTY_NAME__' => $keyPropertyNames];
        }

        if (!is_array($keyPropertyNames)) {
            // todo exception invalid key property names
        }

        $this->keyPropertyNames = $keyPropertyNames;
    }

    public function getKeyPropertyName($classname) {
        if (is_object($classname)) {
            $classname = get_class($classname);
        }
        if (!is_string($classname)) {
            return null;
        }
        if (is_string($classname) && !class_exists($classname)) {
            return null;
        }
        $keyPropertyNames = $this->keyPropertyNames;
        if (!array_key_exists($classname, $keyPropertyNames)) {
            $classname = '__DEFAULT_KEY_PROPERTY_NAME__';
        }
        return $keyPropertyNames[$classname] ?? null;
    }

    public function getKeyFromElement($element) {
        if (!is_object($element)) {
            return null;
        }
        $keyPropertyName = $this->getKeyPropertyName($element) ?? null;
        $isReadable = $this->isPropertyValueReadable($element, $keyPropertyName);
        $elementKey = $keyPropertyName && $isReadable ?
            $this->getPropertyValue($element, $keyPropertyName) :
            null;
        if (empty($elementKey) || !is_string($elementKey)) {
            $elementKey = null;
        }
        return $elementKey;
    }

    private function assertExpectedElementKey($providedKey, $element) {
        $elementKey = $this->getKeyFromElement($element) ?? null;
        if ($elementKey && is_string($providedKey) && $elementKey !== $providedKey) {
            // todo exception key's must match
        }
    }

    protected function getComposedChildren() {
        $children = $this->children;
        if (!$children) {
            $children = $this->initializeComposedChildren();
        }
        return $children;
    }

    protected function initializeComposedChildren(array $elements = []) {
        $keyedElements = [];
        foreach ($elements as $key => $value) {
            $this->assertElementIsAcceptable($value);
            $this->assertExpectedElementKey($key, $value);
            if (!is_string($key)) {
                $valueKey = $this->getKeyFromElement($value) ?? null;
                $key = $valueKey ?? $key;
            }
            $keyedElements[$key] = $value;
        }

        $children = new ArrayCollection($keyedElements);
        $this->children = $children;
        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        return $this->getComposedChildren()->first();
    }

    /**
     * {@inheritdoc}
     */
    public function last()
    {
        return $this->getComposedChildren()->last();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->getComposedChildren()->key();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->getComposedChildren()->next();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->getComposedChildren()->current();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return $this->getComposedChildren()->remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement($element)
    {
        return $this->getComposedChildren()->removeElement($element);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            $this->add($value);
            return;
        }

        $this->set($offset, $value);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function containsKey($key)
    {
        return $this->getComposedChildren()->containsKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($element)
    {
        return $this->getComposedChildren()->contains($element);
    }

    /**
     * {@inheritdoc}
     */
    public function indexOf($element)
    {
        return $this->getComposedChildren()->indexOf($element);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->getComposedChildren()->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        return $this->getComposedChildren()->getKeys();
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->getComposedChildren()->getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->getComposedChildren()->count();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->assertElementIsAcceptable($value);
        $this->assertExpectedElementKey($key, $value);
        if (!is_string($key)) {
            $valueKey = $this->getKeyFromElement($value) ?? null;
            $key = $valueKey ?? $key;
        }
        $this->getComposedChildren()->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function add($element)
    {
        return $this->getComposedChildren()->add($element);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->getComposedChildren()->isEmpty();
    }

    /**
     * Required by interface IteratorAggregate.
     *
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->getComposedChildren()->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function exists(Closure $predicate)
    {
        return $this->getComposedChildren()->exists($predicate);
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function map(Closure $func)
    {
        return $this->getComposedChildren()->map($func);
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function filter(Closure $predicate)
    {
        return $this->getComposedChildren()->filter($predicate);
    }

    /**
     * {@inheritdoc}
     */
    public function forAll(Closure $predicate)
    {
        return $this->getComposedChildren()->forAll($predicate);
    }

    /**
     * {@inheritdoc}
     */
    public function partition(Closure $predicate)
    {
        return $this->getComposedChildren()->partition($predicate);
    }

    /**
     * {@inheritdoc}
     */
    public function slice($offset, $length = null)
    {
        return $this->getComposedChildren()->slice($offset, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->getComposedChildren()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function matching(Criteria $criteria)
    {
        return $this->getComposedChildren()->matching($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->getComposedChildren()->clear();
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return self::class . '@' . spl_object_hash($this);
    }
}
