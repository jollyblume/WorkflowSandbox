<?php

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use App\Workflow\Marking\Marking;

class MarkingTest extends TestCase
{
    public function testGetMarkingId()
    {
        $marking = new Marking('test.marking-id.1', array('a' => 1));
        $this->assertEquals('test.marking-id.1', $marking->getMarkingId());
    }

    public function testToString() {
        $marking = new Marking('test.marking-id.1', array('a' => 1));
        $expectedString = get_class($marking) . '@' . spl_object_hash($marking);
        $this->assertEquals($expectedString, strval($marking));
    }
}
