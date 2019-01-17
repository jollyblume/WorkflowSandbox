<?php

namespace App\Tests\Collection;

use App\Workflow\Marking\MarkingCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class MarkingCollectionTest extends TestCase
{
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new MarkingCollection('marking-store-1', $elements);
        return $collection;
    }

    protected function buildAcceptableElement($key = null) {
        $element1 = new class($key){
            private $markingId;
            public function __construct($markingId) {
                $this->markingId = $markingId;
            }
            public function getMarkingId() {
                return $this->markingId;
            }
        };
        return $element1;
    }

    public function testGetMarkingStoreId() {
        $collection = $this->buildCollection();
        $this->assertEquals('marking-store-1', $collection->getMarkingStoreId());
    }

    public function testToString() {
        $collection = $this->buildCollection();
        $expectedString = get_class($collection) . '@' . spl_object_hash($collection);
        $this->assertEquals($expectedString, strval($collection));
    }

    public function testIsStrictCollectionMembershipTrue() {
        $collection = $this->buildCollection();
        $this->assertTrue($collection->isStrictCollectionMembership());
    }

    public function testGetKeyAwarePropertyNameDefault() {
        $collection = $this->buildCollection();
        $this->assertEquals('markingId', $collection->getKeyAwarePropertyNameDefault());
    }

    public function testHasKeyAwarePropertyNameDefaultIsTrue() {
        $collection = $this->buildCollection();
        $this->assertTrue($collection->hasKeyAwarePropertyNameDefault());
    }

    /** @expectedException \App\Exception\OutOfScopeException */
    public function testStrictAssertElementAllowedInCollectionThrowsIfElementHasNoKeyProperty() {
        $element1 = new class(){};
        $collection = $this->buildCollection();
        $elements = [
            $element1,
        ];
        foreach ($elements as $element) {
            $collection->add($element);
        }
    }

    /** @expectedException \Exception */
    public function testStrictAssertElementAllowedInCollectionThrowsIfElementHasNullKeyProperty() {
        $element1 = $this->buildAcceptableElement();
        $collection = $this->buildCollection();
        $elements = [
            $element1,
        ];
        foreach ($elements as $element) {
            $collection->add($element);
        }
    }

    /** @expectedException \Exception */
    public function testStrictAssertElementAllowedInCollectionThrowsIfElementHasEmptyKeyProperty() {
        $element1 = $this->buildAcceptableElement();
        $collection = $this->buildCollection();
        $elements = [
            $element1,
        ];
        foreach ($elements as $element) {
            $collection->add($element);
        }
    }

    public function testSTRICTAssertKeyAllowedInCollectionOkIfKeyNotString() {
        $element1 = $this->buildAcceptableElement('test.key');
        $collection = $this->buildCollection();
        $elements = [
            99 => $element1,
        ];
        foreach ($elements as $key => $element) {
            $collection->set($key, $element);
        }
        $this->assertCount(1, $collection->toArray());
    }

    /** @expectedException \App\Exception\OutOfScopeException */
    public function testSTRICTAssertKeyAllowedInCollectionFailsIfElementNotObject() {
        $collection = $this->buildCollection();
        $elements = [
            99 => 'notanobject',
        ];
        foreach ($elements as $key => $element) {
            $collection->set($key, $element);
        }
        $this->assertCount(1, $collection->toArray());
    }

    public function testSTRICTAssertKeyAllowedInCollectionOkIfKeyNullAndObjectValid() {
        $element1 = $this->buildAcceptableElement('test.key');
        $collection = $this->buildCollection();
        $collection->set(null, $element1);
        $this->assertCount(1, $collection->toArray());
    }

    public function testSTRICTAssertKeyAllowedInCollectionOkIfKeyAndObjectMatch() {
        $element1 = $this->buildAcceptableElement('test.key');
        $collection = $this->buildCollection();
        $elements = [
            'test.key' => $element1,
        ];
        foreach ($elements as $key => $element) {
            $collection->set($key, $element);
        }
        $this->assertCount(1, $collection->toArray());
    }

    /** @expectedException \App\Exception\OutOfScopeException */
    public function testAssertKeyAllowedInCollectionFailsIfKeyAndObjectMismatch() {
        $element1 = $this->buildAcceptableElement('test.key');
        $collection = $this->buildCollection();
        $elements = [
            'test.key.mismatch' => $element1,
        ];
        foreach ($elements as $key => $element) {
            $collection->set($key, $element);
        }
    }
}
