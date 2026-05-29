<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use Attribute;
use FloorPlanBuilder\webservice\models\RackStatus;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FromRequestBody
{
    private string $class;

    public function __construct(
        private readonly string $model
    )
    {}

    public function getModel(): string
    {
        return $this->model;
    }

    public function setClass(string $class): static
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @throws ReflectionException
     */
    private function formatToGivingModel(array $inputList): object
    {
        $model = $this->getModel();
        return $this->hydrate($model, $inputList);
    }

    /**
     * @throws ReflectionException
     */
    private function hydrate(string $modelClass, array $data): object
    {
        $instance = new $modelClass();
        $ref = new ReflectionClass($modelClass);

        foreach ($data as $k => $v) {
            try {
                $prop = $ref->getProperty($k);
                $propType = $prop->getType()->getName();
                $listItemAttrs = $prop->getAttributes(ListItem::class);

                if (count($listItemAttrs) > 0 && is_array($v)) {
                    /** @var ListItem $listItem */
                    $listItem = $listItemAttrs[0]->newInstance();
                    $subModel = $listItem->getModel();
                    $instance->$k = array_map(function ($subItem) use ($subModel) {
                        if (!is_array($subItem)) {
                            return $subItem;
                        }
                        return $this->hydrate($subModel, $subItem);
                    }, $v);
                } else {
                    if (str_contains($propType, '\\') && (new ReflectionClass($propType))->isEnum()) {
                        /* @var RackStatus $propType*/
                        $instance->$k = $propType::from($v);
                    }
                    else {
                        $instance->$k = $v;
                    }
                }
            } catch (ReflectionException) {
                $instance->$k = $v;
            }
        }

        return $instance;
    }

    /**
     * @throws ReflectionException
     */
    public function setValue(string $property): static
    {
        $value = file_get_contents('php://input');
        if (!$value) {
            return $this;
        }
        $ref = new ReflectionProperty($this->class, $property);
        $ref->setValue($this->formatToGivingModel(json_decode($value, true)));

        return $this;
    }
}