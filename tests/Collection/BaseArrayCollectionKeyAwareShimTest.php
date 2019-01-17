<?php

namespace App\Tests\Collection;

use Doctrine\Common\Collections\Criteria;
use App\Exception\OutOfScopeException;

abstract class BaseArrayCollectionKeyAwareShimTest extends BaseArrayCollectionTest {
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
                'test.id.aD' => $this->buildAcceptableElement('test.id.aD'),
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

    public function testEmpty() : void
    {
        $element = $this->provideDifferentElements()['indexed'][0][0];
        $collection = $this->buildCollection();
        self::assertTrue($collection->isEmpty(), 'Empty collection');

        $collection->add($element);
        self::assertFalse($collection->isEmpty(), 'Not empty collection');
    }

    public function testRemove() : void
    {
        $elements   = $this->provideDifferentElements()['associative'][0];
        $collection = $this->buildCollection($elements);
        self::assertEquals($elements['test.id.aA'], $collection->remove('test.id.aA'));
        unset($elements['test.id.aA']);
        self::assertEquals(null, $collection->remove('non-existent'));
        unset($elements['non-existent']);
        self::assertEquals($elements['test.id.aC'], $collection->remove('test.id.aC'));
        unset($elements['test.id.aC']);
        self::assertEquals(null, $collection->remove('non-existent'));
        unset($elements['non-existent']);

        $expected = [
            'test.id.aB' => $elements['test.id.aB'],
            'test.id.aD' => $elements['test.id.aD'],
        ];
        self::assertEquals($expected, $collection->toArray());
    }

    public function testRemoveElement() : void
    {
        $elements   = $this->provideDifferentElements()['associative'][0];
        $missingElement = $this->buildAcceptableElement('test.id.zZ');
        $collection = $this->buildCollection($elements);
        foreach (['test.id.aA', 'test.id.aC'] as $key) {
            self::assertTrue($collection->removeElement($elements[$key]));
            unset($elements[$key]);
        }
        self::assertFalse($collection->removeElement($missingElement));
        $expected = [
            'test.id.aB' => $elements['test.id.aB'],
            'test.id.aD' => $elements['test.id.aD'],
        ];
        self::assertEquals($expected, $collection->toArray());
    }

    public function testContainsKey() : void
    {
        $elements   = $this->provideDifferentElements()['associative'][0];
        $missingElement = $this->buildAcceptableElement('test.id.zZ');
        $collection = $this->buildCollection($elements);
        foreach ($elements as $key => $element) {
            $this->assertTrue($collection->containsKey($key));
        }
        $this->assertFalse($collection->containsKey('test.id.zZ'));
    }

    public function testContains() : void
    {
        $elements   = $this->provideDifferentElements()['associative'][0];
        $missingElement = $this->buildAcceptableElement('test.id.zZ');
        $collection = $this->buildCollection($elements);
        foreach ($elements as $key => $element) {
            $this->assertTrue($collection->contains($element));
        }
        $this->assertFalse($collection->contains($missingElement));
    }

    public function testExists() : void
    {
        $elements   = $this->provideDifferentElements()['associative'][0];
        $missingElement = $this->buildAcceptableElement('test.id.zZ');
        $collection = $this->buildCollection($elements);
        self::assertTrue($collection->exists(static function ($key, $element) use($elements) {
            return $key === 'test.id.aA' && $element === $elements['test.id.aA'];
        }), 'Element exists');

        self::assertFalse($collection->exists(static function ($key, $element) use($missingElement) {
            return $key === 'test.id.aA' && $element === $missingElement;
        }), 'Element not exists');
    }

    public function testIndexOf() : void
    {
        $elements   = $this->provideDifferentElements()['associative'][0];
        $missingElement = $this->buildAcceptableElement('test.id.zZ');
        $collection = $this->buildCollection($elements);
        $expectedSearch = array_search('test.id.aA', $elements, true);
        $actualSearch = $collection->indexOf('test.id.aA');
        self::assertSame($expectedSearch, $actualSearch, 'Index of test.id.aA');
        $expectedMissing = array_search('test.id.aA', $elements, true);
        $actualMissing = $collection->indexOf('test.id.aA');
        self::assertSame($expectedMissing, $actualMissing, 'Index of non existent');
    }

    public function testGet() : void
    {
        $elements   = $this->provideDifferentElements()['associative'][0];
        $missingElement = $this->buildAcceptableElement('test.id.zZ');
        $collection = $this->buildCollection($elements);
        self::assertSame($elements['test.id.aA'], $collection->get('test.id.aA'), 'Get element by index');
        self::assertSame(null, $collection->get('test.id.zZ'), 'Get non existent element');
    }

    public function testMatchingWithSortingPreservesyKeys() : void
    {
        $objectA = $this->buildAcceptableElement('test.id.aA');
        $objectB = $this->buildAcceptableElement('test.id.aB');

        $objectA->sortField = 2;
        $objectB->sortField = 1;

        $collection = $this->buildCollection([
            'test.id.aA' => $objectA,
            'test.id.aB' => $objectB,
        ]);

        if (! $this->isSelectable($collection)) {
            $this->markTestSkipped('Collection does not support Selectable interface');
        }

        self::assertSame(
            [
                'test.id.aB' => $objectB,
                'test.id.aA' => $objectA,
            ],
            $collection
                ->matching(new Criteria(null, ['sortField' => Criteria::ASC]))
                ->toArray()
        );
    }

    public function testMultiColumnSortAppliesAllSorts() : void
    {
        $this->markTestSkipped('need to fix this one');

        $elements   = $this->provideDifferentElements()['associative'][0];
        $collection = $this->buildCollection([
            ['test.id.aA' => $elements['test.id.aA'], 'test.id.aB' => $elements['test.id.aB']],
            ['test.id.aB' => $elements['test.id.aB'], 'test.id.aD' => $elements['test.id.aD']],
            ['test.id.aB' => $elements['test.id.aB'], 'test.id.aC' => $elements['test.id.aC']],
        ]);

        $expected = [
            1 => ['foo' => 2, 'bar' => 4],
            2 => ['foo' => 2, 'bar' => 3],
            0 => ['foo' => 1, 'bar' => 2],
        ];

        if (! $this->isSelectable($collection)) {
            $this->markTestSkipped('Collection does not support Selectable interface');
        }

        self::assertSame(
            $expected,
            $collection
                ->matching(new Criteria(null, ['test.id.aA' => Criteria::DESC, 'test.id.aB' => Criteria::DESC]))
                ->toArray()
        );
    }
}
