<?php

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use App\Workflow\Marking\MarkingCollection;

class MarkingCollectionTest extends TestCase
{
    public function testGetMarkingId() {
        $collection = new MarkingCollection('test.marking-id.1');
        $this->assertEquals('test.marking-id.1', $collection->getMarkingId());
    }
}
