<?php

/**
 * forked from doctrine/collections
 */

namespace App\Tests\Collection;

use Doctrine\Common\Collections\Collection;
use stdClass;
use function count;
use function is_array;
use function is_numeric;
use function is_string;

abstract class BaseCollectionKeyAwareShimTest extends BaseCollectionTest
{
    abstract protected function buildAcceptableElement(string $keyValue, bool $otherValue = false);

    public function testIssetAndUnset() : void
    {
        $element = $this->buildAcceptableElement('test.element');
        self::assertFalse(isset($this->collection['test.element']));
        $this->collection->add($element);
        self::assertTrue(isset($this->collection['test.element']));
        unset($this->collection['test.element']);
        self::assertFalse(isset($this->collection['test.element']));
    }

    public function testRemovingNonExistentEntryReturnsNull() : void
    {
        self::assertEquals(null, $this->collection->remove('testing_does_not_exist'));
    }

    public function testExists() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection->add($element1);
        $this->collection->add($element2);
        $exists = $this->collection->exists(static function ($k, $e) use ($element1) {
            return $e === $element1;
        });
        self::assertTrue($exists);
        $exists = $this->collection->exists(static function ($k, $e) use ($element3) {
            return $e === $element3;
        });
        self::assertFalse($exists);
    }

    public function testMap() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $this->collection->add($element1);
        $this->collection->add($element2);
        $res = $this->collection->map(static function ($e) {
            return $e->getName();
        });
        $expected = [
            'test.element1' => 'test.element1',
            'test.element2' => 'test.element2',
        ];
        self::assertEquals($expected, $res->toArray());
    }

    public function testFilter() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection->add($element1);
        $this->collection->add($element2);
        $this->collection->add($element3);
        $res = $this->collection->filter(static function ($e) {
            return 'test.element2' === $e->getName();
        });
        self::assertEquals(['test.element2' => $element2], $res->toArray());
    }

    public function testFilterByValueAndKey() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $element4 = $this->buildAcceptableElement('test.element4');
        $element5 = $this->buildAcceptableElement('test.element5');
        $this->collection->add($element1);
        $this->collection->add($element2);
        $this->collection->add($element3);
        $this->collection->add($element4);
        $this->collection->add($element5);
        $res = $this->collection->filter(static function ($v, $k) use ($element4) {
            $vFilter = boolval('test.element2' === $k);
            $kFilter = boolval($element4 === $v);
            $filter = boolval($vFilter || $kFilter);
            return $filter;
        });
        $expected = [
            'test.element2' => $element2,
            'test.element4' => $element4,
        ];
        self::assertSame($expected, $res->toArray());
    }

    public function testFirstAndLast() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection->add($element1);
        $this->collection->add($element2);
        $this->collection->add($element3);

        self::assertEquals($this->collection->first(), $element1);
        self::assertEquals($this->collection->last(), $element3);
    }

    public function testArrayAccess() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection->add($element1);
        $this->collection->add($element2);
        $this->collection->add($element3);

        self::assertEquals($this->collection['test.element1'], $element1);
        self::assertEquals($this->collection['test.element3'], $element3);

        unset($this->collection['test.element3']);
        self::assertCount(2, $this->collection);
    }

    public function testContainsKey() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $this->collection['test.element1'] = $element1;
        self::assertTrue($this->collection->containsKey('test.element1'));
    }

    public function testContains() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $this->collection['test.element1'] = $element1;
        self::assertTrue($this->collection->contains($element1));
    }

    public function testSearch() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $this->collection['test.element1'] = $element1;
        self::assertEquals('test.element1', $this->collection->indexOf($element1));
    }

    public function testGet() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $this->collection['test.element1'] = $element1;
        self::assertEquals($element1, $this->collection->get('test.element1'));
    }

    public function testGetKeys() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection[] = $element1;
        $this->collection[] = $element2;
        $this->collection[] = $element3;
        $expected = [
            'test.element1',
            'test.element2',
            'test.element3',
        ];
        self::assertEquals($expected, $this->collection->getKeys());
    }

    public function testGetValues() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection[] = $element1;
        $this->collection[] = $element2;
        $this->collection[] = $element3;
        $expected = [
            $element1,
            $element2,
            $element3,
        ];
        self::assertEquals($expected, $this->collection->getValues());
    }

    public function testCount() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection[] = $element1;
        $this->collection[] = $element2;
        $this->collection[] = $element3;
        self::assertEquals(3, $this->collection->count());
        self::assertEquals(3, count($this->collection));
    }

    public function testForAll() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection[] = $element1;
        $this->collection[] = $element2;
        $this->collection[] = $element3;
        self::assertEquals($this->collection->forAll(static function ($k, $e) {
            return is_string($k);
        }), true);
        self::assertEquals($this->collection->forAll(static function ($k, $e) {
            return is_array($k);
        }), false);
    }

    public function testPartition() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1', false);
        $element2 = $this->buildAcceptableElement('test.element2', true);
        $this->collection[] = $element1;
        $this->collection[] = $element2;
        $partition          = $this->collection->partition(static function ($k, $e) {
            return $e->getOther() === true;
        });
        self::assertEquals($element2, $partition[0]['test.element2']);
        self::assertEquals($element1, $partition[1]['test.element1']);
    }

    public function testClear() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection[] = $element1;
        $this->collection[] = $element2;
        $this->collection[] = $element3;
        $this->collection->clear();
        self::assertCount(0, $this->collection);
    }

    public function testRemove() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $missingElement = $this->buildAcceptableElement('test.element3');
        $this->collection[] = $element1;
        $this->collection[] = $element2;
        $el                 = $this->collection->remove('test.element2');

        self::assertEquals($element2, $el);
        self::assertFalse($this->collection->contains('test.element2'));
        self::assertNull($this->collection->remove('test.element3'));
    }

    public function testRemoveElement() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $missingElement = $this->buildAcceptableElement('test.element3');
        $this->collection[] = $element1;
        $this->collection[] = $element2;

        self::assertTrue($this->collection->removeElement($element2));
        self::assertFalse($this->collection->contains('test.element2'));
        self::assertFalse($this->collection->removeElement($missingElement));
    }

    public function testSlice() : void
    {
        $element1 = $this->buildAcceptableElement('test.element1');
        $element2 = $this->buildAcceptableElement('test.element2');
        $element3 = $this->buildAcceptableElement('test.element3');
        $this->collection[] = $element1;
        $this->collection[] = $element2;
        $this->collection[] = $element3;

        $slice = $this->collection->slice(0, 1);
        self::assertInternalType('array', $slice);
        self::assertEquals(['test.element1' => $element1], $slice);

        $slice = $this->collection->slice(1);
        self::assertEquals(['test.element2' => $element2, 'test.element3' => $element3], $slice);

        $slice = $this->collection->slice(1, 1);
        self::assertEquals(['test.element2' => $element2], $slice);
    }

    public function testCanRemoveNullValuesByKey() : void
    {
        $this->markTestSkipped('Not applicable');
    }

    public function testCanVerifyExistingKeysWithNullValues() : void
    {
        $this->markTestSkipped('Not applicable');
    }
}
