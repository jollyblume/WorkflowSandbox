<?php

namespace App\Tests\Collection;

use App\Collection\ComposedArrayCollection;
use Doctrine\Common\Collections\Collection;

class ComposedCollectionDoctrineArrayCollectionCompatibilityTest extends BaseArrayCollectionTest {
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new ComposedArrayCollection($elements);
        return $collection;
    }
}
