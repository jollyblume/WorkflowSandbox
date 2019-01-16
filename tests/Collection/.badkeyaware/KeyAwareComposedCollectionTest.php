<?php

namespace App\Tests\Collection;

use App\Collection\KeyAwareComposedArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class KeyAwareComposedCollectionTest extends TestCase
{
    protected function buildCollection($keyPropertyNames, array $elements = []) : Collection {
        $collection = new KeyAwareComposedArrayCollection($keyPropertyNames, $elements);
        return $collection;
    }

    public function testToString() {
        $collection = $this->buildCollection('name');
        $expectedString = get_class($collection) . '@' . spl_object_hash($collection);
        $this->assertEquals($expectedString, strval($collection));
    }

    public function testGetKeyPropetyNameReturnsNullForArrayElement() {
        $collection = $this->buildCollection('name');
        $keyPropertyName = $collection->getKeyPropertyName([]);
        $this->assertNull($keyPropertyName);
    }

    public function testGetKeyPropetyNameReturnsNullForNumericElement() {
        $collection = $this->buildCollection('name');
        $keyPropertyName = $collection->getKeyPropertyName(5);
        $this->assertNull($keyPropertyName);
    }

    public function testGetKeyPropetyNameReturnsDefaultNameWhenClassnameIsNotConfigured() {
        $keyPropertyNames = [
            get_class() => 'test',
            'name',
        ];
        $collection = $this->buildCollection($keyPropertyNames);
        $element = new KeyAwareComposedArrayCollection('test-crap');
        $keyPropertyName = $collection->getKeyPropertyName($element);
        $this->assertEquals('name', $keyPropertyName);
    }

    public function testGetKeyPropetyNameReturnsExpectedNameWhenClassnameIsConfigured() {
        $keyPropertyNames = [
            get_class() => 'test',
            'name',
        ];
        $collection = $this->buildCollection($keyPropertyNames);
        $keyPropertyName = $collection->getKeyPropertyName($this);
        $this->assertEquals('test', $keyPropertyName);
    }

    public function testGetKeyFromElementReturnsNullForArrayElement() {
        $keyPropertyNames = [
            get_class() => 'test',
            'name',
        ];
        $collection = $this->buildCollection($keyPropertyNames);
        $keyName = $collection->getKeyFromElement([]);
        $this->assertNull($keyName);
    }

    public function testGetKeyFromElementReturnsNullForNumericElement() {
        $keyPropertyNames = [
            get_class() => 'test',
            'name',
        ];
        $collection = $this->buildCollection($keyPropertyNames);
        $keyName = $collection->getKeyFromElement(5);
        $this->assertNull($keyName);
    }

    public function testGetKeyFromElementReturnsNullForObjectElementWithoutKeyProperty() {
        $keyPropertyNames = [
            get_class() => 'test',
            'name',
        ];
        $collection = $this->buildCollection($keyPropertyNames);
        $keyName = $collection->getKeyFromElement($this);
        $this->assertNull($keyName);
    }

    public function testGetKeyFromElementReturnsNullForObjectElementWithEmptyKeyPropertyValue() {
        $element = new class() {
            public function getName() {
                return 'test.key';
            }
        };
        $keyPropertyNames = [
            get_class($element) => 'name',
            get_class() => 'test',
            'name',
        ];
        $collection = $this->buildCollection($keyPropertyNames);
        $keyName = $collection->getKeyFromElement($element);
        $this->assertEquals('test.key', $keyName);
    }

    public function testGetKeyFromElementReturnsExpectedNameForObjectElementWithKeyPropertyValueSet() {
        $element = new class() {
            public function getName() {
                return '';
            }
        };
        $keyPropertyNames = [
            get_class($element) => 'name',
            get_class() => 'test',
            'name',
        ];
        $collection = $this->buildCollection($keyPropertyNames);
        $keyName = $collection->getKeyFromElement($element);
        $this->assertNull($keyName);
    }

}
