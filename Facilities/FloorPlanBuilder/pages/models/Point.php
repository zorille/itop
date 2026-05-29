<?php

namespace FloorPlanBuilder\webservice\models;

class Point
{
    public float $x;
    public float $y;

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }
}