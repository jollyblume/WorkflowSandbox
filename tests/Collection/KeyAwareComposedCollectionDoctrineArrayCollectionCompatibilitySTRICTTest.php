<?php

namespace App\Tests\Collection;

use App\Collection\KeyAwareComposedArrayCollection;
use Doctrine\Common\Collections\Collection;

class KeyAwareComposedCollectionDoctrineArrayCollectionCompatibilitySTRICTTest extends BaseArrayCollectionKeyAwareShimTest {
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new KeyAwareComposedArrayCollection($elements, 'name', true);
        return $collection;
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
