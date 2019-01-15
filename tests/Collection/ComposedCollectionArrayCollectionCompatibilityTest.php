<?php

namespace App\Tests\Collection;

use App\Collection\ComposedCollection;
use Doctrine\Common\Collections\Collection;

class ComposedCollectionArrayCollectionCompatibilityTest extends BaseArrayCollectionTest {
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new ComposedCollection($elements);
        return $collection;
    }
}
