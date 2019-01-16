<?php

namespace App\Tests\Collection;

use App\Collection\KeyAwareComposedArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class KeyAwareComposedCollectionTest extends TestCase
{
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new KeyAwareComposedArrayCollection($elements);
        return $collection;
    }

    public function testToString() {
        $collection = $this->buildCollection();
        $expectedString = get_class($collection) . '@' . spl_object_hash($collection);
        $this->assertEquals($expectedString, strval($collection));
    }
}
