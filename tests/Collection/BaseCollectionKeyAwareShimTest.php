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
    abstract protected function buildAcceptableElement(string $keyValue);

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
        $this->collection->add('one');
        $this->collection->add('two');

        self::assertEquals($this->collection->first(), 'one');
        self::assertEquals($this->collection->last(), 'two');
    }

    public function testArrayAccess() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        self::assertEquals($this->collection[0], 'one');
        self::assertEquals($this->collection[1], 'two');

        unset($this->collection[0]);
        self::assertEquals($this->collection->count(), 1);
    }

    public function testContainsKey() : void
    {
        $this->collection[5] = 'five';
        self::assertTrue($this->collection->containsKey(5));
    }

    public function testContains() : void
    {
        $this->collection[0] = 'test';
        self::assertTrue($this->collection->contains('test'));
    }

    public function testSearch() : void
    {
        $this->collection[0] = 'test';
        self::assertEquals(0, $this->collection->indexOf('test'));
    }

    public function testGet() : void
    {
        $this->collection[0] = 'test';
        self::assertEquals('test', $this->collection->get(0));
    }

    public function testGetKeys() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        self::assertEquals([0, 1], $this->collection->getKeys());
    }

    public function testGetValues() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        self::assertEquals(['one', 'two'], $this->collection->getValues());
    }

    public function testCount() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        self::assertEquals($this->collection->count(), 2);
        self::assertEquals(count($this->collection), 2);
    }

    public function testForAll() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        self::assertEquals($this->collection->forAll(static function ($k, $e) {
            return is_string($e);
        }), true);
        self::assertEquals($this->collection->forAll(static function ($k, $e) {
            return is_array($e);
        }), false);
    }

    public function testPartition() : void
    {
        $this->collection[] = true;
        $this->collection[] = false;
        $partition          = $this->collection->partition(static function ($k, $e) {
            return $e === true;
        });
        self::assertEquals($partition[0][0], true);
        self::assertEquals($partition[1][0], false);
    }

    public function testClear() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection->clear();
        self::assertEquals($this->collection->isEmpty(), true);
    }

    public function testRemove() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $el                 = $this->collection->remove(0);

        self::assertEquals('one', $el);
        self::assertEquals($this->collection->contains('one'), false);
        self::assertNull($this->collection->remove(0));
    }

    public function testRemoveElement() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        self::assertTrue($this->collection->removeElement('two'));
        self::assertFalse($this->collection->contains('two'));
        self::assertFalse($this->collection->removeElement('two'));
    }

    public function testSlice() : void
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection[] = 'three';

        $slice = $this->collection->slice(0, 1);
        self::assertInternalType('array', $slice);
        self::assertEquals(['one'], $slice);

        $slice = $this->collection->slice(1);
        self::assertEquals([1 => 'two', 2 => 'three'], $slice);

        $slice = $this->collection->slice(1, 1);
        self::assertEquals([1 => 'two'], $slice);
    }

    protected function fillMatchingFixture() : void
    {
        $std1               = new stdClass();
        $std1->foo          = 'bar';
        $this->collection[] = $std1;

        $std2               = new stdClass();
        $std2->foo          = 'baz';
        $this->collection[] = $std2;
    }

    public function testCanRemoveNullValuesByKey() : void
    {
        $this->collection->add(null);
        $this->collection->remove(0);
        self::assertTrue($this->collection->isEmpty());
    }

    public function testCanVerifyExistingKeysWithNullValues() : void
    {
        $this->collection->set('key', null);
        self::assertTrue($this->collection->containsKey('key'));
    }
}
