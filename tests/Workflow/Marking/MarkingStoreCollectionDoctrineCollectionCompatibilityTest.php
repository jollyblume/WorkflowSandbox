<?php

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use App\Workflow\Marking\MarkingStoreCollection;
use App\Tests\Collection\BaseCollectionTest;

class MarkingStoreCollectionDoctrineCollectionCompatibilityTest extends BaseCollectionTest
{
    public function setup() {
        $this->collection = new MarkingStoreCollection('test.marking-store-id.1');
    }
}
