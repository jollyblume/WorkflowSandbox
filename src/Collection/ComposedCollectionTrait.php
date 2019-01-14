<?php

/**
 * forked from doctrine/collections
 */

namespace App\Collection;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use App\Exception\ExceptionContext;
use App\Exception\PropImmutableException;

/**
 * ComposedCollectionTrait
 *
 * ComposedCollectionTrait composes a doctrine/collections ArrayCollection
 * called $children and exposes its interfaces, Collection and Selectable,
 *
 * ComposedCollectionTrait implements concrete accessor methods for all of the
 * methods defined by ComposedCollectionInterface and composes a Docrtine
 * ArrayCollection.
 *
 * Some  accessor method implementations are designed to enforce constraints on
 * the composed collection's elements. They forward the method call to a
 * semantic accessor method, instead of forwarding the method call directly to
 * the $children collection.
 *
 * The method name for semantic accessor methods is created using sprintf:
 *  $methodName = sprintf($template, $semanticParameter);
 *
 *  where, $semanticParameter is a string that must be set in the constructor.
 *      and
 *  where, $template is one of:
 *    - remove%sKey
 *    - remove%s
 *    - has%sKey
 *    - has%s
 *    - get%s
 *    - set%s
 *    - add%s
 *
 * Concrete methods for each $template must be implemented.
 */
trait ComposedCollectionTrait
{
    use \App\Traits\PropertyAccessorTrait;

    /**
     * This string MUST be set in the using class's construtor.
     *  ei: $this->semanticParameter = 'MySemanticCollectionName';
     *
     * The following semantic collection accessor methods will also need to be
     * defined:
     *  - remove%sKey
     *  - remove%s
     *  - has%sKey
     *  - has%s
     *  - get%s
     *  - set%s
     *  - add%s
     *
     *  , where %s is $semanticParameter
     *
     * The method names generated from these templates are executed throughout
     * this trait's implementation.
     *
     * @var string $semanticParameter
     */
    private $semanticParameter;

    /**
     * Name of the property containing the elements key
     *
     * @var string $nameProperty
     */
    private $keyProperty = 'name';

    /**
     * composedCollection
     *
     * @var ArrayCollection $children
     */
    private $children = new ArrayCollection();

    protected function removeChildKey(string $key) {
        return $this->children->remove($key);
    }

    protected function removeChild($child) {
        return $this->children->removeElement($child);
    }

    protected function hasChildKey() {
        return $this->children->containsKey(string $key);
    }

    protected function hasChild($child) {
        return $this->children->contains($child);
    }

    protected function getChild(string $key) {
        return $this->children->get($key);
    }

    protected function setChild(string $key, $child) {
        $this->children->set($key, $child);
        return $this;
    }

    protected function addChild($child) {
        $keyProperty = $this->keyProperty;
        $key = $this->getPropertyValue($child, $keyProperty);
        $this->setChild(string $key, $child);
    }

    protected function addChildIfMissing($child) {
        $keyProperty = $this->keyProperty;
        $key = $this->getPropertyValue($child, $keyProperty);
        if (!$this->hasChildKey($key)) {
            $this->setChild(string $key, $child);
        }
    }


    /**
     * Use sprintf to build a method name from a template and $semanticParameter.
     *
     * These method names are used by this trait to call public accessor methods
     * defined in the enclosing class.
     *
     * @param string $template
     *
     * @return string
     */
    private function getAccessorMethodName(string $template) :string
    {
        $semanticParameter = $this->semanticParameter;
        $methodName = sprintf($template, $semanticParameter);
        return $methodName;
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        return $this->children->first();
    }

    /**
     * {@inheritdoc}
     */
    public function last()
    {
        return $this->children->last();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->children->key();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->children->next();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->children->current();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $method = $this->getAccessorMethodName('remove%sKey');

        return $this->$method($key);
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement($element)
    {
        $method = $this->getAccessorMethodName('remove%s');

        return $this->$method($element);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $method = $this->getAccessorMethodName('has%sKey');

        return $this->$method($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $method = $this->getAccessorMethodName('get%s');

        return $this->$method($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $method = $this->getAccessorMethodName('set%s');

        return $this->$method($offset, $value);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $method = $this->getAccessorMethodName('remove%sKey');

        return $this->$method($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function containsKey($key)
    {
        $method = $this->getAccessorMethodName('has%sKey');

        return $this->$method($key);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($element)
    {
        $method = $this->getAccessorMethodName('has%s');

        return $this->$method($element);
    }

    /**
     * {@inheritdoc}
     */
    public function indexOf($element)
    {
        return $this->children->indexOf($element);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $method = $this->getAccessorMethodName('get%s');

        return $this->$method($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        return $this->children->getKeys();
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->children->getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->children->count();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $method = $this->getAccessorMethodName('set%s');

        return $this->$method($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function add($element)
    {
        $method = $this->getAccessorMethodName('add%s');

        return $this->$method($element);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->children->isEmpty();
    }

    /**
     * Required by interface IteratorAggregate.
     *
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->children->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function exists(Closure $predicate)
    {
        return $this->children->exists($predicate);
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function map(Closure $func)
    {
        return $this->children->map($func);
    }

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function filter(Closure $predicate)
    {
        return $this->children->filter($predicate);
    }

    /**
     * {@inheritdoc}
     */
    public function forAll(Closure $predicate)
    {
        return $this->children->forAll($predicate);
    }

    /**
     * {@inheritdoc}
     */
    public function partition(Closure $predicate)
    {
        return $this->children->partition($predicate);
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->children->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function slice($offset, $length = null)
    {
        return $this->children->slice($offset, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->children->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function matching(Criteria $criteria)
    {
        return $this->children->matching($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        throw new PropImmutableException();
    }
}
