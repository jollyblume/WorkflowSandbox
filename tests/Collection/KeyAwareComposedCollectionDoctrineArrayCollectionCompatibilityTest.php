<?php

namespace App\Tests\Collection;

use App\Collection\KeyAwareComposedArrayCollection;
use Doctrine\Common\Collections\Collection;

class KeyAwareComposedCollectionDoctrineArrayCollectionCompatibilityTest extends BaseArrayCollectionTest {
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new KeyAwareComposedArrayCollection($elements);
        return $collection;
    }
}
