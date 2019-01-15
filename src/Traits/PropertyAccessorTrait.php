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

    protected function getPropertyValue(object $object, string $property) {
        $propertyAccessor = $this->getPropertyAccessor();
        return $propertyAccessor->getValue($object, $property);
    }

    protected function setPropertyValue(object $object, string $property, $value) :self {
        $propertyAccessor = $this->getPropertyAccessor();
        $propertyAccessor->setValue($object, $property, $value);
        return $this;
    }

    protected function isPropertyValueReadable(object $object, string $property) :bool {
        $propertyAccessor = $this->getPropertyAccessor();
        $isReadable = $propertyAccessor->isReadable($object, $property);
        return $isReadable;
    }
}
