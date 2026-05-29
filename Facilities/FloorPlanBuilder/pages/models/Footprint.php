<?php

namespace FloorPlanBuilder\webservice\models;

use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\ListItem;

class Footprint
{
    public string $id;
    public string $name;
    public ?string $color = null;
    public int $height;
    public int $width;
    public ?int $rotation;
    #[ListItem(Point::class)]
    public ?array $units = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getRotation(): int
    {
        return $this->rotation ?? 0;
    }

    public function getUnits(): array
    {
        return $this->units ?? [];
    }
}