<?php

namespace App\Tests\Collection;

use App\Workflow\Marking\MarkingCollection;
use App\Tests\Collection\BaseCollectionKeyAwareShimTest;
use Doctrine\Common\Collections\Collection;

class MarkingCollectionDoctrineCollectionCompatibilitySTRICTTest extends BaseCollectionKeyAwareShimTest {
    public function setup() {
        $this->collection = new MarkingCollection('marking-store-1');
    }

    protected function buildAcceptableElement(string $keyValue, bool $otherValue = false) {
        $element = new class($keyValue, $otherValue) {
            private $markingId;
            private $other;
            public function __construct(string $markingId, bool $otherValue) {
                $this->markingId = $markingId;
                $this->other = $otherValue;
            }
            public function getMarkingId() {
                return $this->markingId;
            }
            public function getOther() {
                return $this->other;
            }
        };
        return $element;
    }
}
