<?php

namespace App\Tests\Collection;

use App\Collection\KeyAwareComposedArrayCollection;

class KeyAwareComposedCollectionDoctrineCollectionCompatibilitySTRICTTest extends BaseCollectionKeyAwareShimTest {
    public function setup() {
        $this->collection = new KeyAwareComposedArrayCollection([], 'name', true);
    }
    
    protected function buildAcceptableElement(string $keyValue) {
        $element = new class($keyValue) {
            private $key;
            public function __construct(string $key) {
                $this->key = $key;
            }
            public function getName() {
                return $this->key;
            }
        };
        return $element;
    }
}
