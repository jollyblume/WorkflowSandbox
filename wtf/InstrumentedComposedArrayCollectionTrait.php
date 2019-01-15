<?php

/**
 * forked from doctrine/collections
 */

namespace App\Collection;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use App\Exception\OutOfScopeException;
use App\Traits\PropertyAccessorTrait;
use App\Traits\PropRequiredException;
trait InstrumentedComposedArrayCollectionTrait
{
    use PropertyAccessorTrait;

    /**
     * composedCollection
     *
     * @var ArrayCollection $children
     */
    private $children;

    /**
     * If set, $keyName is the object's property name containing it's key field.
     *
     * Non-object values are ignored.
     *
     * @var string|null $autokeyPropertyName
     */
    private $autokeyPropertyName;

    protected function getComposedChildren() {
        $children = $this->children;
        if (!$children) {
            $children = $this->initializeComposedChildren();
        }
        return $children;
    }

    protected function initializeComposedChildren(array $elements = []) {
        $autokeyPropertyName = $this->autokeyPropertyName;
        $children = $autokeyPropertyName ?
            new ArrayCollection() :
            new ArrayCollection($elements);
        $this->children = $children;
        if ($autokeyPropertyName) {
            foreach ($elements as $key => $value) {
                $this->set($key, $value);
            }
        }
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
        $autokeyPropertyName = $this->autokeyPropertyName;
        $isReadable = $autokeyPropertyName && is_object($value) ?
            $this->isPropertyValueReadable($value, $autokeyPropertyName) :
            false;
        if ($isReadable) {
            $valueKey = $this->getPropertyValue($value, $autokeyPropertyName);
            if (!$valueKey) {
                $contextParameters = [
                    'key' => $key,
                ];
                throw new PropRequiredException($contextParameters);
            }
            if (null !== $key && is_string($key) && $key !== $valueKey) {
                $contextParameters = [
                    'key' => $key,
                    'valueKey' => $valueKey,
                ];
                throw new OutOfScopeException($contextParameters);
            }
            $key = $valueKey;
        }
        $this->getComposedChildren()->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function add($element)
    {
        $autokeyPropertyName = $this->autokeyPropertyName;
        if ($autokeyPropertyName && is_object($element) && $this->isPropertyValueReadable($element, $autokeyPropertyName)) {
            $this->set(null, $value);
            return;
        }
        $this->getComposedChildren()->add($element);
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
