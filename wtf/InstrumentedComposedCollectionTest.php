<?php

namespace App\Tests\Collection;

use App\Collection\InstrumentedComposedArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class InstrumentedComposedCollectionTest extends TestCase {
    protected function buildCollection(array $elements = [], ?string $autokeyPropertyName = null) : Collection {
        $collection = new InstrumentedComposedArrayCollection($elements, $autokeyPropertyName);
        return $collection;
    }

    private function getTestElement(?string $keyValue = null) {
        if (null === $keyValue) {
            $element = new class() {};
        }
        if (null !== $keyValue) {
            $element = new class($keyValue) {
                private $keyValue;
                public function __construct(string $keyValue) {
                    $this->keyValue = $keyValue;
                }
                public function getName() {
                    return $this->keyValue;
                }
            };
        }
        return $element;
    }

    public function testInitializeComposedChildrenFixesMissingKeysWhenAutokeySet() {
        $elements = [
            $this->getTestElement('test.key.1'),
            $this->getTestElement('test.key.2'),
            $this->getTestElement(),
            $this->getTestElement('test.key.4'),
        ];
        $collection = $this->buildCollection($elements, 'name');
        $expectedElements = [
            'test.key.1' => $elements[0],
            'test.key.2' => $elements[1],
            0 => $elements[2],
            'test.key.4' => $elements[3],
        ];
        $this->assertEquals($expectedElements, $collection->toArray());
    }

    // public function testInitializeComposedChildrenIgnoresMissingKeysWhenAutokeyNotSet() {
    //     $elements = [
    //         $this->getTestElement('test.key.1'),
    //         $this->getTestElement('test.key.2'),
    //         $this->getTestElement(),
    //         $this->getTestElement('test.key.4'),
    //     ];
    //     $collection = $this->buildCollection($elements);
    //     $expectedElements = [
    //         0 => $elements[0],
    //         1 => $elements[1],
    //         2 => $elements[2],
    //         3 => $elements[3],
    //     ];
    //     $this->assertEquals($expectedElements, $collection->toArray());
    // }
}
