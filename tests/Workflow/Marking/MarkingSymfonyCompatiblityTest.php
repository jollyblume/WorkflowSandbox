<?php

/**
 * forked from symfony/workflow
 */

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use App\Workflow\Marking\Marking;

class MarkingSymfonyCompatiblityTest extends TestCase
{
    public function testMarking()
    {
        $marking = new Marking('name', array('a' => 1));

        $this->assertTrue($marking->has('a'));
        $this->assertFalse($marking->has('b'));
        $this->assertSame(array('a' => 1), $marking->getPlaces());

        $marking->mark('b');

        $this->assertTrue($marking->has('a'));
        $this->assertTrue($marking->has('b'));
        $this->assertSame(array('a' => 1, 'b' => 1), $marking->getPlaces());

        $marking->unmark('a');

        $this->assertFalse($marking->has('a'));
        $this->assertTrue($marking->has('b'));
        $this->assertSame(array('b' => 1), $marking->getPlaces());

        $marking->unmark('b');

        $this->assertFalse($marking->has('a'));
        $this->assertFalse($marking->has('b'));
        $this->assertSame(array(), $marking->getPlaces());
    }
}
