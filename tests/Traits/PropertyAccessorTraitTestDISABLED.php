<?php

namespace App\Tests\Traits;

use App\Traits\PropertyAccessorTrait;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class PropertyAccessorTraitTest  extends TestCase {
    public function setUp() {
        $this->testInternalPropertyAccessor = null;
    }

    private function invokeMethodOnTrait(string $methodName, array $parameters = [])
    {
        $trait = $this->getMockForTrait(PropertyAccessorTrait::class);

        $reflection = new \ReflectionClass(get_class($trait));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($trait, $parameters);
    }

    private function getTestFixture(array $initialData = []) {
        $testFixture = new ArrayCollection($initialData);
        return $testFixture;
    }

    private $testInternalPropertyAccessor;

    private function getPropertyAccessor() {
        $propertyAccessor = $this->testInternalPropertyAccessor;
        if (!$propertyAccessor) {
            $propertyAccessor = $this->getPropertyAccessorFromTrait();
            // todo test retval instanceof PropertyAccessor
            $this->testInternalPropertyAccessor = $propertyAccessor;
        }
        return $propertyAccessor;
    }

    private function getPropertyAccessorFromTrait() {
        $propertyAccessor = $this->invokeMethodOnTrait(
            'getPropertyAccessor',
            []
        );
        return $propertyAccessor;
    }

    private function getPropertyValueUsingTrait($object, string $property) {
        $value = $this->invokeMethodOnTrait(
            'getPropertyValue',
            [
                $object,
                $property,
            ]
        );
        return $value;
    }

    private function setPropertyValueUsingTrait($object, string $property, $value) {
        $this->invokeMethodOnTrait(
            'setPropertyValue',
            [
                $object,
                $property,
                $value,
            ]
        );
    }

    private function isPropertyValueReadableUsingTrait($object, string $property) {
        $value = $this->invokeMethodOnTrait(
            'isPropertyValueReadable',
            [
                $object,
                $property,
            ]
        );
        return $value;
    }

    public function testIsPropertyValueReadableIsTrueWhenPropertyUnset() {
        $testee = $this->getTestFixture();
        $value = $this->isPropertyValueReadableUsingTrait($testee, '[testProperty]');
        $this->assertTrue($value);
    }

    public function testIsPropertyValueReadableIsTrueWhenPropertySet() {
        $testee = $this->getTestFixture(['testProperty' => 'test.string']);
        $value = $this->isPropertyValueReadableUsingTrait($testee, '[testProperty]');
        $this->assertTrue($value);
    }

    public function testGetPropertyValueReturnsExpectedValueWhenValueSetToString() {
        $testee = $this->getTestFixture(['testProperty' => 'test.string']);
        $value = $this->getPropertyValueUsingTrait($testee, '[testProperty]');
        $this->assertEquals($testee['testProperty'], $value);
    }

    public function testGetPropertyValueReturnsNullWhenPropertyUnset() {
        $testee = $this->getTestFixture();
        $value = $this->getPropertyValueUsingTrait($testee, '[testProperty]');
        $this->assertNull($value);
    }

    public function testSetPropertyValueSetsValueWhenPropertyUnset() {
        $testee = $this->getTestFixture();
        $this->setPropertyValueUsingTrait($testee, '[testProperty]', 'test.string');
        $this->assertEquals($testee['testProperty'], 'test.string');
    }
}
