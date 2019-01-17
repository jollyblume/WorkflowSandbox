<?php

namespace App\Tests\Collection;

use App\Workflow\Marking\MarkingStoreCollection;
use App\Tests\Collection\BaseArrayCollectionKeyAwareShimTest;
use Doctrine\Common\Collections\Collection;

class MarkingStoreCollectionDoctrineArrayCollectionCompatibilitySTRICTTest extends BaseArrayCollectionKeyAwareShimTest {
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new MarkingStoreCollection('marking-store-collection-1', $elements);
        return $collection;
    }

    protected function buildAcceptableElement(string $keyValue) {
        $element = new class($keyValue) {
            private $markingStoreId;
            public function __construct(string $markingStoreId) {
                $this->markingStoreId = $markingStoreId;
            }
            public function getMarkingStoreId() {
                return $this->markingStoreId;
            }
        };
        return $element;
    }
}
