<?php

namespace FloorPlanBuilder\webservice\models;

use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\ListItem;

class Layer
{
    public int $id;
    public string $name;
    #[ListItem(Point::class)]
    public array $walls;
    #[ListItem(Point::class)]
    public array $pillars;
    #[ListItem(Pod::class)]
    public array $pods;
    #[ListItem(Rack::class)]
    public array $racks;
    #[ListItem(Circuit::class)]
    public array $circuits;
    #[ListItem(Footprint::class)]
    public array $footprints;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Point[]
     */
    public function getWalls(): array
    {
        return $this->walls;
    }

    /**
     * @return Point[]
     */
    public function getPillars(): array
    {
        return $this->pillars;
    }

    /**
     * @return Pod[]
     */
    public function getPods(): array
    {
        return $this->pods;
    }

    /**
     * @return Rack[]
     */
    public function getRacks(): array
    {
        return $this->racks;
    }

    /**
     * @return Circuit[]
     */
    public function getCircuits(): array
    {
        return $this->circuits;
    }

    /**
     * @return Footprint[]
     */
    public function getFootprints(): array
    {
        return $this->footprints;
    }
}