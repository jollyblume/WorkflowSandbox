<?php

namespace App\Tests\Collection;

use App\Workflow\Marking\MarkingStoreCollection;
use App\Tests\Collection\BaseCollectionKeyAwareShimTest;
use Doctrine\Common\Collections\Collection;

class MarkingStoreCollectionDoctrineCollectionCompatibilitySTRICTTest extends BaseCollectionKeyAwareShimTest {
    public function setup() {
        $this->collection = new MarkingStoreCollection('marking-store-collection-1');
    }

    protected function buildAcceptableElement(string $keyValue, bool $otherValue = false) {
        $element = new class($keyValue, $otherValue) {
            private $markingStoreId;
            private $other;
            public function __construct(string $markingStoreId, bool $otherValue) {
                $this->markingStoreId = $markingStoreId;
                $this->other = $otherValue;
            }
            public function getMarkingStoreId() {
                return $this->markingStoreId;
            }
            public function getOther() {
                return $this->other;
            }
        };
        return $element;
    }
}
