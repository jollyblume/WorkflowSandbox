<?php

/**
 * forked from doctrine/collections
 */

namespace App\Tests\Collection;

use App\Collection\ComposedArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class ComposedCollectionTest extends TestCase
{
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new ComposedArrayCollection($elements);
        return $collection;
    }

    public function testToString() {
        $collection = $this->buildCollection();
        $expectedString = get_class($collection) . '@' . spl_object_hash($collection);
        $this->assertEquals($expectedString, strval($collection));
    }
}
