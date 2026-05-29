<?php
namespace FloorPlanBuilder\webservice\webservice;

use ArchivedObjectException;
use CoreCannotSaveObjectException;
use CoreException;
use CoreUnexpectedValue;
use DBObject;
use DBObjectSet;
use DBSearch;
use FloorPlanBuilder\webservice\models\BuilderBody;
use FloorPlanBuilder\webservice\models\Circuit;
use FloorPlanBuilder\webservice\models\Footprint;
use FloorPlanBuilder\webservice\models\Rack;
use MetaModel;
use MySQLException;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\CircuitElecs;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\Footprints;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\FromItopParam;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\FromRequestBody;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\Instance;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\Racks;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\Response;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\webservice\Ws;
use OQLException;

class MainPut extends Ws
{
    #[FromItopParam]
    public static int $locationId;

    #[FromRequestBody(BuilderBody::class)]
    public static ?BuilderBody $data = null;

    #[Instance]
    public static Racks $racks;

    #[Instance]
    public static Footprints $footprints;

    #[Instance]
    public static CircuitElecs $circuitElecs;

    /**
     * @throws ArchivedObjectException
     * @throws CoreException
     * @throws CoreUnexpectedValue
     */
    private static function saveJsonData(DBObject $object, string $newJsonData): void
    {
        if ($object->Get('jsondata') !== $newJsonData) {
            $object->Set('jsondata', $newJsonData);
        }
    }

    /**
     * @throws CoreException
     * @throws CoreUnexpectedValue
     */
    private static function saveName(DBObject $object, string $newName): void
    {
        if ($object->GetName() !== $newName) {
            $object->Set('name', $newName);
        }
    }

    /**
     * @throws CoreException
     * @throws CoreUnexpectedValue
     * @throws OQLException
     * @throws CoreCannotSaveObjectException
     * @throws ArchivedObjectException
     */
    private static function updateRoom(): void
    {
        $locationId = static::$locationId;
        $locationName = static::$data->getRoomName();

        $layers = static::$data->getLayers();
        $roomLayer = array_values(array_filter($layers, static fn($layer) => $layer->getName() === 'FloorPlanBuilder:Layers:Walls:Title'))[0] ?? null;

        $location = MetaModel::GetObject('Location', $locationId);
        if ($location?->GetName() !== $locationName) {
            $location?->Set('name', $locationName);
            $location?->DBUpdate();
        }

        if (!is_null($roomLayer)) {
            $floorPlanBuilder = MetaModel::GetObjectFromOQL(
                'SELECT FloorPlanBuilder WHERE location_id=:location_id',
                ['location_id' => $locationId]
            );

            if (is_null($floorPlanBuilder)) {
                MetaModel::NewObject('FloorPlanBuilder', [
                    'location_id' => $locationId,
                    'jsondata' => json_encode([
                        'walls' => $layers[0]->getWalls(),
                        'pillars' => $layers[0]->getPillars()
                    ])
                ])->DBInsert();
            }
            else {
                $newJsonData = json_encode([
                    'walls' => $layers[0]->getWalls(),
                    'pillars' => $layers[0]->getPillars()
                ]);
                if ($floorPlanBuilder?->Get('jsondata') !== $newJsonData) {
                    $floorPlanBuilder?->Set('jsondata', $newJsonData);
                }
                $floorPlanBuilder?->DBUpdate();
            }
        }
    }

    /**
     * @throws CoreException
     * @throws CoreUnexpectedValue
     * @throws OQLException
     * @throws CoreCannotSaveObjectException
     * @throws ArchivedObjectException
     * @throws MySQLException
     */
    private static function updateCircuits(): void
    {
        $layers = static::$data->getLayers();
        $circuitsLayer = array_values(array_filter($layers, static fn($layer) => $layer->getName() === 'FloorPlanBuilder:Layers:Circuits:Title'))[0] ?? null;

        if (is_null($circuitsLayer)) {
            return;
        }

        $circuits = static::$circuitElecs->getFromLocation(static::$locationId, false);

        /** @var DBObject $circuit */
        foreach ($circuits as $circuit) {
            /** @var Circuit $newCircuitAssociated */
            $newCircuitAssociated = array_values(array_filter(
                $circuitsLayer->getCircuits(),
                static fn($c) => $c->getId() === (int)$circuit->GetKey()
            ))[0] ?? null;

            if (is_null($newCircuitAssociated)) {
                continue;
            }

            static::saveJsonData($circuit, json_encode([
                'x' => $newCircuitAssociated->getX(),
                'y' => $newCircuitAssociated->getY(),
            ]));
            static::saveName($circuit, $newCircuitAssociated->getName());

            $circuit->DBUpdate();
        }
    }

    /**
     * @throws CoreException
     * @throws CoreUnexpectedValue
     * @throws OQLException
     * @throws CoreCannotSaveObjectException
     * @throws ArchivedObjectException
     * @throws MySQLException
     */
    private static function updateFootprints(): void
    {
        $layers = static::$data->getLayers();
        $footprintLayer = array_values(array_filter($layers, static fn($layer) => $layer->getName() === 'FloorPlanBuilder:Layers:Footprints:Title'))[0] ?? null;

        if (is_null($footprintLayer)) {
            return;
        }

        $footprints = static::$footprints->getFromLocation(static::$locationId, false);

        /** @var DBObject $footprint */
        foreach ($footprints as $footprint) {
            /** @var Footprint $newFootprintAssociated */
            $newFootprintAssociated = array_values(array_filter(
                $footprintLayer->getFootprints(),
                static fn($f) => (int)$f->getId() === (int)$footprint->GetKey()
            ))[0] ?? null;

            if ($newFootprintAssociated === null) {
                continue;
            }

            static::saveJsonData($footprint, json_encode([
                'color' => $newFootprintAssociated->getColor(),
                'height' => $newFootprintAssociated->getHeight(),
                'width' => $newFootprintAssociated->getWidth(),
                'units' => $newFootprintAssociated->getUnits(),
                'rotation' => $newFootprintAssociated->getRotation(),
            ]));
            static::saveName($footprint, $newFootprintAssociated->getName());

            $footprint->DBUpdate();
        }
    }

    /**
     * @throws CoreException
     * @throws CoreUnexpectedValue
     * @throws OQLException
     * @throws CoreCannotSaveObjectException
     * @throws ArchivedObjectException
     * @throws MySQLException
     */
    private static function updateRacks(): void
    {
        $layers = static::$data->getLayers();
        $rackLayer = array_values(array_filter($layers, static fn($layer) => $layer->getName() === 'FloorPlanBuilder:Layers:Racks:Title'))[0] ?? null;

        if (is_null($rackLayer)) {
            return;
        }

        $racks = static::$racks->getFromLocation(static::$locationId, false);

        /** @var DBObject $rack */
        foreach ($racks as $rack) {
            /** @var Rack $newRackAssociated */
            $newRackAssociated = array_values(array_filter(
                $rackLayer->getRacks(),
                static fn($r) => $r->getId() === (int)$rack->GetKey()
            ))[0];

            if ($newRackAssociated === null) {
                continue;
            }

            static::saveJsonData($rack, json_encode([
                'x' => $newRackAssociated->getX(),
                'y' => $newRackAssociated->getY(),
                'rotation' => $newRackAssociated->getRotation(),
                'podId' => $newRackAssociated->getPodId()
            ]));
            static::saveName($rack, $newRackAssociated->getName());

            $rack->DBUpdate();
        }
    }

    /**
     * @throws ArchivedObjectException
     * @throws CoreException
     */
    protected static function onSuccess(Response $r): void
    {
        /* Update Location object */
        static::updateRoom();
        /* Circuit objects */
        static::updateCircuits();
        /* Footprint objects */
        static::updateFootprints();
        /* Rack objects */
        static::updateRacks();

        $r->success();
    }
}