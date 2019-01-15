<?php

namespace App\Tests\Collection;

use App\Collection\ComposedCollection;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ComposedCollectionTest extends TestCase {
    private function getElement(?string $keyValue = null) {
        $element = null === $keyValue ?
            new class() {
                public function isKeyed() {
                    return false;
                }
            } :
            new class($keyValue) {
                private $keyValue;
                public function __construct(string $keyValue) {
                    $this->keyValue = $keyValue;
                }
                public function isKeyed() {
                    return true;
                }
                public function getName() {
                    return $this->keyValue;
                }
            };
        return $element;
    }

    public function testGetElementReturnsUnkeyedClassIfArg1IsNull() {
        $element = $this->getElement();
        $this->assertFalse($element->isKeyed());
    }

    public function testGetElementReturnsKeyedClassIfArg1IsNotNull() {
        $element = $this->getElement('test.name');
        $this->assertTrue($element->isKeyed());
        $this->assertEquals('test.name', $element->getName());
    }

    public function testGetKeyPropertyReturnsNullByDefault() {
        $collection = new ComposedCollection();
        $this->assertNull($collection->getKeyProperty());
    }

    public function testGetKeyPropertyReturnsExpectedValueWhenDefined() {
        $collection = new ComposedCollection([], 'name');
        $this->assertEquals('name', $collection->getKeyProperty());
    }

    public function testGetSemanticParameterReturnsChildByDefault() {
        $collection = new ComposedCollection();
        $this->assertEquals('Child', $collection->getSemanticParameter());
    }

    // public function testAddChildAddsObjectsWhenKeyPropertyIsNotSet() {
    //     $collection = new ComposedCollection();
    //     $element = $this->getElement('test.name');
    //     $collection[] = $element;
    //     $this->assertFalse($collection->containsKey('test.name'));
    //     $this->assertTrue($collection->contains($element));
    // }
    // 
    // public function testAddChildSetsObjectsWhenKeyPropertyIsSet() {
    //     $collection = new ComposedCollection([], 'name');
    //     $element = $this->getElement('test.name');
    //     $collection[] = $element;
    //     $this->assertTrue($collection->containsKey('test.name'));
    // }
    //
    // /**
    //  * @expectedException \App\Exception\OutOfScopeException
    //  */
    // public function testSetChildThrowsWhenKeyNotMatchChildKeyField() {
    //     $collection = new ComposedCollection([], 'name');
    //     $element = $this->getElement('test.name');
    //     $collection['wrong.name'] = $element;
    //     $this->assertTrue($collection->containsKey('test.name'));
    // }
    //
    // public function testConstructorInitializedCollection() {
    //     $collection = new ComposedCollection([
    //         'test.element.1' => $this->getElement(),
    //         'test.element.2' => $this->getElement(),
    //     ]);
    //     $this->assertTrue($collection->containsKey('test.element.1'));
    // }
}
