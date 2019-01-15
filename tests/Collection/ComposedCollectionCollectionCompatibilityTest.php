<?php

namespace App\Tests\Collection;

use App\Collection\ComposedCollection;
use Doctrine\Common\Collections\Collection;

class ComposedCollectionCollectionCompatibilityTest extends BaseCollectionTest {
    public function setup() {
        $this->collection = new ComposedCollection();
    }
}
