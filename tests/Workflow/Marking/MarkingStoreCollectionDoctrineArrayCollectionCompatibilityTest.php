<?php

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;
use App\Workflow\Marking\MarkingStoreCollection;
use App\Tests\Collection\BaseArrayCollectionTest;

class MarkingStoreCollectionDoctrineArrayCollectionCompatibilityTest extends BaseArrayCollectionTest
{
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new MarkingStoreCollection('test.marking-store-id.1', $elements);
        return $collection;
    }
}
