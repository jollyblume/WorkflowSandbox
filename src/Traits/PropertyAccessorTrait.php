<?php

namespace App\Traits;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

trait PropertyAccessorTrait
{
    /**
     * @var PropertyAccessorInterface $propertyAccessor
     */
    private $propertyAccessor;

    protected function getPropertyAccessor() {
        $propertyAccessor = $this->propertyAccessor;
        if (!$propertyAccessor) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $this->propertyAccessor = $propertyAccessor;
        }
        return $propertyAccessor;
    }

    private function getPropertyValue($object, string $property) {
        $propertyAccessor = $this->getPropertyAccessor();
        return $propertyAccessor->getValue($object, $property);
    }

    private function setPropertyValue($object, string $property, $value) {
        $propertyAccessor = $this->getPropertyAccessor();
        $propertyAccessor->setValue($object, $property, $value);
    }
}
