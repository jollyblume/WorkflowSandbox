<?php

namespace App\Tests\Collection;

use App\Collection\KeyAwareComposedArrayCollection;

class KeyAwareComposedCollectionDoctrineCollectionCompatibilitySTRICTTest extends BaseCollectionKeyAwareShimTest {
    public function setup() {
        $this->collection = new KeyAwareComposedArrayCollection([], 'name', true);
    }

    protected function buildAcceptableElement(string $keyValue, bool $otherValue = false) {
        $element = new class($keyValue, $otherValue) {
            private $key;
            private $other;
            public function __construct(string $key, bool $otherValue) {
                $this->key = $key;
                $this->other = $otherValue;
            }
            public function getName() {
                return $this->key;
            }
            public function getOther() {
                return $this->other;
            }
        };
        return $element;
    }
}
