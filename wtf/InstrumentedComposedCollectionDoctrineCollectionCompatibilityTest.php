<?php

namespace App\Tests\Collection;

use App\Collection\InstrumentedComposedArrayCollection;

class InstrumentedComposedCollectionDoctrineCollectionCompatibilityTest extends BaseCollectionTest {
    public function setup() {
        $this->collection = new InstrumentedComposedArrayCollection([], 'name');
    }
}
