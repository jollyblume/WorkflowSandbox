<?php

namespace App\Tests\Collection;

use App\Workflow\Marking\MarkingCollection;
use Doctrine\Common\Collections\Collection;

class MarkingCollectionDoctrineArrayCollectionCompatibilitySTRICTTest extends BaseArrayCollectionKeyAwareShimTest {
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new MarkingCollection('marking-store-1', $elements);
        return $collection;
    }

    protected function buildAcceptableElement(string $keyValue) {
        $element = new class($keyValue) {
            private $markingId;
            public function __construct(string $markingId) {
                $this->markingId = $markingId;
            }
            public function getMarkingId() {
                return $this->markingId;
            }
        };
        return $element;
    }
}
