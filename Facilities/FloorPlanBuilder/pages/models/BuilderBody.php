<?php

namespace FloorPlanBuilder\webservice\models;

use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\ListItem;

class BuilderBody
{
    public int $roomId;
    public string $roomName;
    #[ListItem(Layer::class)]
    public array $layers;

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    /**
     * @return Layer[]
     */
    public function getLayers(): array
    {
        return $this->layers;
    }
}