<?php

namespace FloorPlanBuilder\webservice\models;

class Rack
{
    public int $id;
    public string $name;
    public ?string $podId;
    public int $roomId;
    public ?int $rotation = null;
    public ?float $x = null;
    public ?float $y = null;
    public string $orgId = '1';
    public RackStatus $status = RackStatus::OTHER;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPodId(): ?string
    {
        return $this->podId;
    }

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    public function getRotation(): ?int
    {
        return $this->rotation;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function getOrgId(): string
    {
        return $this->orgId;
    }

    public function getStatus(): RackStatus
    {
        return $this->status;
    }
}