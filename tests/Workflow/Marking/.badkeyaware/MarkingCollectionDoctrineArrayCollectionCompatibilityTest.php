<?php

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;
use App\Workflow\Marking\MarkingCollection;
use App\Tests\Collection\BaseArrayCollectionTestShimTest;

class MarkingCollectionDoctrineArrayCollectionCompatibilityTest extends BaseArrayCollectionTestShimTest
{
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new MarkingCollection('test.marking-store-id.1', $elements);
        return $collection;
    }

    protected function buildAcceptableElement(string $keyValue) {
        $element = new class($keyValue) {
            private $key;
            public function __construct(string $key) {
                $this->key = $key;
            }
            public function getMarkingId() :string {
                $key = $this->key;
                return $key;
            }
        };
        return $element;
    }
}
