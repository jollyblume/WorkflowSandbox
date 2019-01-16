<?php

namespace App\Tests\Workflow\Marking;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;
use App\Workflow\Marking\MarkingCollection;
use App\Tests\Collection\BaseArrayCollectionTest;

class MarkingCollectionDoctrineArrayCollectionCompatibilityTest extends BaseArrayCollectionTest
{
    protected function buildCollection(array $elements = []) : Collection {
        $collection = new MarkingCollection('test.marking-id.1', $elements);
        return $collection;
    }
}
