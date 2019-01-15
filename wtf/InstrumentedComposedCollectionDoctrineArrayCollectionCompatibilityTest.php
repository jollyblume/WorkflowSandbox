<?php

namespace App\Tests\Collection;

use App\Collection\InstrumentedComposedArrayCollection;
use Doctrine\Common\Collections\Collection;

class InstrumentedComposedCollectionDoctrineArrayCollectionCompatibilityTest extends BaseArrayCollectionTest {
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new InstrumentedComposedArrayCollection($elements, 'name');
        return $collection;
    }
}
