<?php

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;
use App\Workflow\Marking\MarkingStoreCollection;
use App\Tests\Collection\BaseArrayCollectionTestShimTest;

class MarkingStoreCollectionDoctrineArrayCollectionCompatibilityTest extends BaseArrayCollectionTestShimTest
{
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new MarkingStoreCollection('test.marking-store-id.1', $elements);
        return $collection;
    }

    protected function buildAcceptableElement(string $keyValue) {
        $element = new class ($keyValue) {
            private $keyValue;
            public function __constuct($keyValue) {
                $this->keyValue = $keyValue;
            }
            public function getMarkingStoreId() {
                return $this->keyValue;
            }
        };
        return $element;
    }
}
