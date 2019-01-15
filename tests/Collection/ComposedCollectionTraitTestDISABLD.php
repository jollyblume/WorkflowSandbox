<?php

namespace App\Tests\Collection;

use App\Collection\ComposedCollectionTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Tests\Common\Collections\;

class ComposedCollectionTraitTest  extends TestCase {
    private function getFixture() {
        $fixture = $this->getMockForTrait(ComposedCollectionTrait::class);
        return $fixture;
    }

    private function invokeMethodOnFixture($fixture, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($fixture));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($fixture, $parameters);
    }

    public function testGetChildrenReturnsAnArrayCollection() {
        $fixture = $this->getFixture();
        $children = $this->invokeMethodOnFixture(
            $fixture,
            'getChildren',
            []
        );
        $this->assertInstanceof(ArrayCollection::class, $children);
    }
}
