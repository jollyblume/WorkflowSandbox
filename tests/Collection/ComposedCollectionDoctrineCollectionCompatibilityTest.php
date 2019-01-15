<?php

namespace App\Tests\Collection;

use App\Collection\ComposedArrayCollection;

class ComposedCollectionDoctrineCollectionCompatibilityTest extends BaseCollectionTest {
    public function setup() {
        $this->collection = new ComposedArrayCollection();
    }
}
