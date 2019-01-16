<?php

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use App\Workflow\Marking\MarkingCollection;
use App\Tests\Collection\BaseCollectionTest;

class MarkingCollectionDoctrineCollectionCompatibilityTest extends BaseCollectionTest
{
    public function setup() {
        $this->collection = new MarkingCollection('test.marking-store-id.1');
    }
}
