<?php

namespace App\Tests\Collection;

use App\Collection\KeyAwareComposedArrayCollection;

class KeyAwareComposedCollectionDoctrineCollectionCompatibilityTest extends BaseCollectionTest {
    public function setup() {
        $this->collection = new KeyAwareComposedArrayCollection('name');
    }
}
