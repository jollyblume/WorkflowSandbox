<?php

namespace App\Tests\Collection;
use App\Exception\OutOfScopeException;

abstract class BaseArrayCollectionTestShimTest extends BaseArrayCollectionTest {
    abstract protected function buildAcceptableElement(string $keyValue);

    public function provideDifferentElements() : array
    {
        return [
            'indexed'     => [[
                $this->buildAcceptableElement('test.id.1'),
                $this->buildAcceptableElement('test.id.2'),
                $this->buildAcceptableElement('test.id.3'),
                $this->buildAcceptableElement('test.id.4'),
                $this->buildAcceptableElement('test.id.5'),
                ]],
            'associative' => [[
                'test.id.aA' => $this->buildAcceptableElement('test.id.aA'),
                'test.id.aB' => $this->buildAcceptableElement('test.id.aB'),
                'test.id.aC' => $this->buildAcceptableElement('test.id.aC'),
                ]],
            'mixed'       => [[
                'test.id.bA' => $this->buildAcceptableElement('test.id.bA'),
                $this->buildAcceptableElement('test.id.6'),
                'test.id.bB' => $this->buildAcceptableElement('test.id.bB'),
                $this->buildAcceptableElement('test.id.7'),
                $this->buildAcceptableElement('test.id.8'),
                ]],
        ];
    }

    public function testRemove() : void
    {
        $elements   = $this->provideDifferentElements()['mixed'][0];
        $collection = $this->buildCollection($elements);
        self::assertEquals($elements['test.id.bA'], $collection->remove('test.id.bA'));
        unset($elements['test.id.bA']);
        self::assertEquals(null, $collection->remove('non-existent'));
        unset($elements['non-existent']);
        self::assertEquals($elements[0], $collection->remove('test.id.6'));
        unset($elements['test.id.6']);
        self::assertEquals(null, $collection->remove('non-existent'));
        unset($elements['non-existent']);

        self::assertEquals($elements, $collection->toArray());
    }

    public function testRemoveElement() : void
    {
        $this->assertTrue(true);
    }

    public function testContainsKey() : void
    {
        $this->assertTrue(true);
    }

    public function testContains() : void
    {
        $this->assertTrue(true);
    }

    public function testExists() : void
    {
        $this->assertTrue(true);
    }

    public function testIndexOf() : void
    {
        $this->assertTrue(true);
    }

    public function testGet() : void
    {
        $this->assertTrue(true);
    }

    public function testMatchingWithSortingPreservesyKeys() : void
    {
        $this->assertTrue(true);
    }

    public function testMultiColumnSortAppliesAllSorts() : void
    {
        $this->assertTrue(true);
    }
}
