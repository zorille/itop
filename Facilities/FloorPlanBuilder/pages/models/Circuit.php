<?php

namespace FloorPlanBuilder\webservice\models;

class Circuit
{
    public int $id;
    public string $name;
    public ?float $x = null;
    public ?float $y = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function getY(): ?float
    {
        return $this->y;
    }
}