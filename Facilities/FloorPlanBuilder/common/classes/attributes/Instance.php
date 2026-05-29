<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use Attribute;
use ReflectionException;
use ReflectionProperty;
use Throwable;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Instance
{
    private string $class;

    public function setClass(string $class): static
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public function setValue(string $property): static
    {
        $ref = new ReflectionProperty($this->class, $property);
        $type = $ref->getType();
        if (!$type->allowsNull()) {
            $ref->setValue(new ($type->getName())());
            return $this;
        }

        if (str_contains($type->getName(), '\\')) {
            $ref->setValue(null);
            return $this;
        }

        try {
            $ref->setValue(new ($type->getName())());
        } catch (Throwable) {
            $ref->setValue(null);
        }

        return $this;
    }
    
    public function setValue2(ReflectionProperty $property): static
    {
        $type = $property->getType();
        if (!$type->allowsNull()) {
            $property->setValue(new ($type->getName())());
            return $this;
        }

        if (str_contains($type->getName(), '\\')) {
            $property->setValue(null);
            return $this;
        }

        try {
            $property->setValue(new ($type->getName())());
        } catch (Throwable) {
            $property->setValue(null);
        }

        return $this;
    }
}