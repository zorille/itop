<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ListItem
{
    public function __construct(
        private readonly string $model
    )
    {
    }

    public function getModel(): string
    {
        return $this->model;
    }
}