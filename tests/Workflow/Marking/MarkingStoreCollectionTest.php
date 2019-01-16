<?php

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use App\Workflow\Marking\MarkingStoreCollection;

class MarkingStoreCollectionTest extends TestCase
{
    public function testGetMarkingStoreId() {
        $collection = new MarkingStoreCollection('test.marking-store-id.1');
        $this->assertEquals('test.marking-store-id.1', $collection->getMarkingStoreId());
    }
}
