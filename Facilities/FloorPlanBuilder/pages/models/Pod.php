<?php

namespace FloorPlanBuilder\webservice\models;

class Pod
{
    public string $id;
    public string $name;

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}