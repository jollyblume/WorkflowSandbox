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
use App\Exception\OutOfScopeException;

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
    private $semanticParameter = 'Child';

    public function getSemanticParameter() {
        return $this->semanticParameter;
    }

    /**
     * Name of the property containing the elements key
     *
     * @var string $nameProperty
     */
    private $keyProperty;

    public function getKeyProperty() {
        return $this->keyProperty;
    }

    /**
     * composedCollection
     *
     * @var ArrayCollection $children
     */
    private $children;

    protected function getComposedChildren() {
        $children = $this->children;
        if (!$children) {
            $children = $this->initializeComposedChildren();
        }
        return $children;
    }

    protected function initializeComposedChildren(array $elements = []) {
        $children = new ArrayCollection($elements);
        $this->children = $children;
        return $children;
    }

    protected function removeChildKey(string $key) {
        return $this->getComposedChildren()->remove($key);
    }

    protected function removeChild($child) {
        return $this->getComposedChildren()->removeElement($child);
    }

    protected function hasChildKey(string $key) {
        return $this->getComposedChildren()->containsKey($key);
    }

    protected function hasChild($child) {
        return $this->getComposedChildren()->contains($child);
    }

    protected function getChild(string $key) {
        return $this->getComposedChildren()->get($key);
    }

    protected function setChild(?string $key, $child) {
        $keyProperty = $this->getKeyProperty();
        if (null !== $key && !empty($keyProperty)) {
            $expectedKey = $this->getPropertyValue($child, $keyProperty);
            if ($expectedKey !== $key) {
                $contextParameters = [
                    'debug' => 'key must match child key field'
                ];
                throw new OutOfScopeException($contextParameters);
            }
        }
        if (null === $key && !empty($keyProperty)) {
            $key = $this->getPropertyValue($child, $keyProperty);
        }
        $this->getComposedChildren()->set($key, $child);
        return $this;
    }

    protected function addChild($child) {
        $keyProperty = $this->getKeyProperty();
        if (is_object($child) && $keyProperty) {
            $this->setChild(null, $child);
        }
        if (!is_object($child) || !$keyProperty) {
            $this->getComposedChildren()->add($child);
        }
        return $this;
    }

    // protected function addChildIfMissing($child) {
    //     if (!$this->hasChild($child)) {
    //         $this->addChild($child);
    //     }
    // }

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
        return $this->getComposedChildren()->indexOf($element);
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
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getComposedChildren()->__toString();
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
        throw new PropImmutableException();
    }
}
