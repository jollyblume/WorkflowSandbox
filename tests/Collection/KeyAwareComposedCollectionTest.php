<?php

namespace App\Tests\Collection;

use App\Collection\KeyAwareComposedArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class KeyAwareComposedCollectionTest extends TestCase
{
    protected function buildCollection(array $elements = [], $propertyNames = null, bool $strict = false) : Collection {
        $collection = new KeyAwareComposedArrayCollection($elements, $propertyNames, $strict);
        return $collection;
    }

    public function testToString() {
        $collection = $this->buildCollection();
        $expectedString = get_class($collection) . '@' . spl_object_hash($collection);
        $this->assertEquals($expectedString, strval($collection));
    }

    public function testIsStrictCollectionMembershipFalseByDefault() {
        $collection = $this->buildCollection();
        $this->assertFalse($collection->isStrictCollectionMembership());
    }

    public function testIsStrictCollectionMembershipTrueWhenSet() {
        $collection = $this->buildCollection([], null, true);
        $this->assertTrue($collection->isStrictCollectionMembership());
    }

    public function testSetKeyAwarePropertyNamesNullBecomesEmptyArray() {
        $collection = $this->buildCollection([], null);
        $this->assertEquals([], $collection->getKeyAwarePropertyNames());
    }

    public function testSetKeyAwarePropertyNamesEmptyArrayBecomesEmptyArray() {
        $collection = $this->buildCollection([], []);
        $this->assertEquals([], $collection->getKeyAwarePropertyNames());
    }

    public function testSetKeyAwarePropertyNamesStringBecomesDefault() {
        $collection = $this->buildCollection([], 'default.property');
        $expected = [
            '__DEFAULT_PROPERTY_NAME__' => 'default.property',
        ];
        $this->assertEquals($expected, $collection->getKeyAwarePropertyNames());
    }

    public function testSetKeyAwarePropertyNamesArrayWithDefaultSet() {
        $expected = [
            '__DEFAULT_PROPERTY_NAME__' => 'default.property',
        ];
        $collection = $this->buildCollection([], $expected);
        $this->assertEquals($expected, $collection->getKeyAwarePropertyNames());
    }

    public function testSetKeyAwarePropertyNamesArrayWithIndexedDefault() {
        $propertyNames = [
            'default.property',
        ];
    $collection = $this->buildCollection([], $propertyNames);
        $this->assertEquals('default.property', $collection->getKeyAwarePropertyNameDefault());
    }

    /** @expectedException \Exception */
    public function testSetKeyAwarePropertyNamesThrowsIfMultipleIndexedDefaults() {
        $propertyNames = [
            'indexed-default.1',
            'indexed-default.2',
        ];
        $collection = $this->buildCollection([], $propertyNames);
    }

    /** @expectedException \Exception */
    public function testSetKeyAwarePropertyNamesThrowsIfBothIndexedAndSpecifiedDefaults() {
        $propertyNames = [
            'indexed-default.1',
            '__DEFAULT_PROPERTY_NAME__' => 'default.property',
        ];
        $collection = $this->buildCollection([], $propertyNames);
    }

    public function testGetKeyAwarePropertyNameDefaultReturnsDefaultIfSet() {
        $collection = $this->buildCollection([], 'default.property');
        $this->assertEquals('default.property', $collection->getKeyAwarePropertyNameDefault());
    }

    public function testGetKeyAwarePropertyNameDefaultReturnsNullIfNotSet() {
        $collection = $this->buildCollection();
        $this->assertNull($collection->getKeyAwarePropertyNameDefault());
    }

    public function testHasKeyAwarePropertyNameDefaultTrueIfDefaultSet() {
        $collection = $this->buildCollection([], 'default.property');
        $this->assertTrue($collection->hasKeyAwarePropertyNameDefault());
    }

    public function testHasKeyAwarePropertyNameDefaultFalseIfDefaultNotSet() {
        $collection = $this->buildCollection();
        $this->assertFalse($collection->hasKeyAwarePropertyNameDefault());
    }

    public function testHasKeyAwarePropertyNamesFalseIfNoneDefined() {
        $collection = $this->buildCollection();
        $this->assertFalse($collection->hasKeyAwarePropertyNames());
    }

    public function testHasKeyAwarePropertyNamesTrueIfDefined() {
        $collection = $this->buildCollection([], 'default.property');
        $this->assertTrue($collection->hasKeyAwarePropertyNames());
    }

    public function testAssertElementAllowedInCollectionOkIfNoPropertyNamesSet() {
        $element1 = new class('key.element1'){
            private $key;
            public function __construct(string $key) {
                $this->key = $key;
            }
            public function getName() {
                return $this->key;
            }
        };
        $element2 = new class(){};
        $collection = $this->buildCollection();
        $elements = [
            1,
            'test.string',
            $element1,
            $element2,
            [],
        ];
        foreach ($elements as $element) {
            $collection->add($element);
        }
        $this->assertCount(count($elements), $collection->toArray());
    }

    public function testAssertElementAllowedInCollectionOkIfPropertyNamesSet() {
        $element1 = new class('key.element1'){
            private $key;
            public function __construct(string $key) {
                $this->key = $key;
            }
            public function getName() {
                return $this->key;
            }
        };
        $element2 = new class(){};
        $collection = $this->buildCollection([], 'name');
        $elements = [
            1,
            'test.string',
            $element1,
            $element2,
            [],
        ];
        foreach ($elements as $element) {
            $collection->add($element);
        }
        $this->assertCount(count($elements), $collection->toArray());
    }

    /** @expectedException \App\Exception\OutOfScopeException */
    public function testStrictAssertElementAllowedInCollectionThrowsIfElementHasNoKeyProperty() {
        $element1 = new class(){};
        $collection = $this->buildCollection([], 'name', true);
        $elements = [
            $element1,
        ];
        foreach ($elements as $element) {
            $collection->add($element);
        }
    }

    /** @expectedException \Exception */
    public function testStrictAssertElementAllowedInCollectionThrowsIfElementHasEmptyKeyProperty() {
        $element1 = new class(){
            private $key;
            public function __construct(string $key = null) {
                $this->key = $key;
            }
            public function getName() {
                return $this->key;
            }
        };
        $collection = $this->buildCollection([], 'name', true);
        $elements = [
            $element1,
        ];
        foreach ($elements as $element) {
            $collection->add($element);
        }
    }

    public function testAssertKeyAllowedInCollectionOkIfKeyNotString() {
        $element1 = new class('test.key'){
            private $key;
            public function __construct(string $key = null) {
                $this->key = $key;
            }
            public function getName() {
                return $this->key;
            }
        };
        $collection = $this->buildCollection([], 'name');
        $elements = [
            99 => $element1,
        ];
        foreach ($elements as $key => $element) {
            $collection->set($key, $element);
        }
        $this->assertCount(1, $collection->toArray());
    }

    public function testSTRICTAssertKeyAllowedInCollectionOkIfKeyNotString() {
        $element1 = new class('test.key'){
            private $key;
            public function __construct(string $key = null) {
                $this->key = $key;
            }
            public function getName() {
                return $this->key;
            }
        };
        $collection = $this->buildCollection([], 'name', true);
        $elements = [
            99 => $element1,
        ];
        foreach ($elements as $key => $element) {
            $collection->set($key, $element);
        }
        $this->assertCount(1, $collection->toArray());
    }

    public function testAssertKeyAllowedInCollectionOkIfElementNotObject() {
        $collection = $this->buildCollection([], 'name');
        $elements = [
            99 => 'notanobject',
        ];
        foreach ($elements as $key => $element) {
            $collection->set($key, $element);
        }
        $this->assertCount(1, $collection->toArray());
    }

    /** @expectedException \App\Exception\OutOfScopeException */
    public function testSTRICTAssertKeyAllowedInCollectionFailsIfElementNotObject() {
        $collection = $this->buildCollection([], 'name', true);
        $elements = [
            99 => 'notanobject',
        ];
        foreach ($elements as $key => $element) {
            $collection->set($key, $element);
        }
        $this->assertCount(1, $collection->toArray());
    }

    public function testSTRICTAssertKeyAllowedInCollectionOkIfKeyNullAndObjectValid() {
        $element1 = new class('test.key'){
            private $key;
            public function __construct(string $key = null) {
                $this->key = $key;
            }
            public function getName() {
                return $this->key;
            }
        };
        $collection = $this->buildCollection([], 'name', true);
        $collection->set(null, $element1);
        $this->assertCount(1, $collection->toArray());
    }

    public function testSTRICTAssertKeyAllowedInCollectionOkIfKeyAndObjectMatch() {
        $element1 = new class('test.key'){
            private $key;
            public function __construct(string $key = null) {
                $this->key = $key;
            }
            public function getName() {
                return $this->key;
            }
        };
        $collection = $this->buildCollection([], 'name', true);
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
        $element1 = new class('test.key'){
            private $key;
            public function __construct(string $key = null) {
                $this->key = $key;
            }
            public function getName() {
                return $this->key;
            }
        };
        $collection = $this->buildCollection([], 'name');
        $elements = [
            'test.key.mismatch' => $element1,
        ];
        foreach ($elements as $key => $element) {
            $collection->set($key, $element);
        }
    }
}
