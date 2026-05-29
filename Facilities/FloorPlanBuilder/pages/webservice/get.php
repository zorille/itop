<?php

namespace FloorPlanBuilder\webservice\webservice;

use JetBrains\PhpStorm\ArrayShape;
use ArchivedObjectException;
use CoreException;
use CoreUnexpectedValue;
use MySQLException;
use OQLException;
use RuntimeException;
use UserRights;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\{CircuitElecs,
    FloorPlanBuilder,
    Footprints,
    FromItopParam,
    Instance,
    Locations,
    Racks,
    Response,
    webservice\Ws};

class MainGet extends Ws
{
    #[FromItopParam]
    public static int $locationId;

    #[Instance]
    public static FloorPlanBuilder $floorPlanBuilder;

    #[Instance]
    public static Locations $locations;

    #[Instance]
    public static Racks $racks;

    #[Instance]
    public static Footprints $footprints;

    #[Instance]
    public static CircuitElecs $circuits;

    private static function formatData(
        #[ArrayShape([
            'id' => "int",
            'name' => "string",
            'friendlyname' => "string",
            'status' => "string",
            'jsondata' => "array",
            'racks' => "array",
            'circuits' => "array",
            'footprints' => "array",
        ])]
        array $data
    ): array
    {
        if ($data['jsondata'] === null) {
            $data['jsondata'] = [
                'walls' => [],
                'pillars' => []
            ];
        }

        return [
            'roomName' => $data['name'],
            'roomId' => $data['id'],
            'layers' => [
                [
                    'id' => 1,
                    'name' => 'FloorPlanBuilder:Layers:Walls:Title',
                    'racks' => [],
                    'pods' => [],
                    'walls' => $data['jsondata']['walls'],
                    'footprints' => [],
                    'circuits' => [],
                    'pillars' => $data['jsondata']['pillars'],
                ],
                [
                    'id' => 2,
                    'name' => 'FloorPlanBuilder:Layers:Circuits:Title',
                    'racks' => [],
                    'pods' => [],
                    'walls' => $data['jsondata']['walls'],
                    'footprints' => [],
                    'circuits' => array_map(static fn($circuit) => [
                        ...array_reduce(
                            array_filter(array_keys($circuit), static fn($k) => $k !== 'jsondata'),
                            static fn(array $r, string $k) => [
                                ...$r,
                                $k => $circuit[$k]
                            ],
                            []
                        ),
                        'x' => $circuit['jsondata']->x,
                        'y' => $circuit['jsondata']->y,
                    ], $data['circuits']),
                    'pillars' => [],
                ],
                [
                    'id' => 3,
                    'name' => 'FloorPlanBuilder:Layers:Footprints:Title',
                    'racks' => [],
                    'pods' => [],
                    'walls' => $data['jsondata']['walls'],
                    'footprints' => $data['footprints'],
                    'circuits' => [],
                    'pillars' => [],
                ],
                [
                    'id' => 4,
                    'name' => 'FloorPlanBuilder:Layers:Racks:Title',
                    'racks' => $data['racks'],
                    'pods' => [],
                    'walls' => $data['jsondata']['walls'],
                    'footprints' => [],
                    'circuits' => [],
                    'pillars' => [],
                ]
            ]
        ];
    }

    /**
     * @throws CoreException
     * @throws MySQLException
     * @throws CoreUnexpectedValue
     * @throws OQLException
     * @throws ArchivedObjectException
     */
    protected static function onSuccess(Response $r): void
    {
        if (!UserRights::IsLoggedIn()) {
            throw new RuntimeException('Not authenticated', 401);
        }

        $iLocationId = static::$locationId;
        if ($iLocationId <= 0) {
            throw new RuntimeException('Missing/invalid location_id', 400);
        }

        // Récupère l’objet Location
        $oLocation = static::$locations->getFromId($iLocationId);
        if ($oLocation === null) {
            throw new RuntimeException('Location not found', 404);
        }

        $r->success(static::formatData([
            'id' => $oLocation->GetKey(),
            'name' => $oLocation->GetName(),
            'friendlyname' => (string)$oLocation->Get('friendlyname'),
            'status' => (string)$oLocation->Get('status'),
            'jsondata' => static::$floorPlanBuilder->getFromLocation($iLocationId)[0]['jsondata'],
            'racks' => static::$racks->getFromLocation($iLocationId),
            'footprints' => static::$footprints->getFromLocation($iLocationId),
            'circuits' => static::$circuits->getFromLocation($iLocationId)
        ]));
    }
}