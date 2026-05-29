<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use Attribute;
use FloorPlanBuilder\webservice\Main;
use ReflectionException;
use ReflectionProperty;
use stdClass;
use utils;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FromItopParam
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
    public function setValue(string $property): void
    {
        $propSnakeCase = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $property));
        $ref = new ReflectionProperty($this->class, lcfirst($property));
        $ref->setValue(utils::ReadParam(
            $propSnakeCase, 0, false,
            ((string)$ref->getType() === 'int' ? 'integer' : (string)$ref->getType())
        ));
    }
}