<?php
namespace FloorPlanBuilder\webservice;

use ArchivedObjectException;
use CoreException;
use CoreUnexpectedValue;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use LoginWebPage;
use MySQLException;
use OQLException;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use UserRights;
use utils;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\{
    CircuitElecs, FloorPlanBuilder,
    Footprints, FromItopParam,
    Locations, Racks, Response
};

require_once dirname(__FILE__).'/../common/classes.php';

class Main
{
    #[FromItopParam]
    public static int $locationId;

    /**
     * @throws ReflectionException
     */
    private static function initializeAttributes(): void
    {
        $ref = new ReflectionClass(static::class);
        $props = $ref->getProperties();
        foreach ($props as $prop) {
            $attrs = $prop->getAttributes(FromItopParam::class);
            if (count($attrs) > 0) {
                /**
                 * @var FromItopParam $instance
                 */
                $instance = $attrs[0]->newInstance();
                $instance->setValue($prop->getName());
            }
        }
    }

    private static function manageSetup(): bool
    {
        // ✅ permet de passer le setup
        $sOp = utils::ReadParam('op', '', false, 'raw_data');
        return $sOp !== 'get_location';
    }

    /**
     * @throws Exception
     */
    private static function manageLogin(): void
    {
        // ✅ IMPORTANT: initialise l'auth iTop (session + user) pour une page custom
        require_once APPROOT.'/application/loginwebpage.class.inc.php';
        // backoffice si ta page est dans la console admin, sinon null pour "n'importe quel GUI"
        LoginWebPage::DoLoginEx('backoffice', false);
    }

    /**
     * @throws Exception
     */
    private static function initialize(): bool
    {
        require_once dirname(__FILE__).'/../../../approot.inc.php';
        require_once APPROOT.'/application/startup.inc.php';

        if (static::manageSetup()) {
            return true;
        }
        static::manageLogin();

        static::initializeAttributes();

        return false;
    }

    private static function formatData(
        #[ArrayShape([
            'id' => "int",
            'name' => "string",
            'friendlyname' => "string",
            'status' => "string",
            'jsondata' => ["array", null],
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
                    'name' => 'Murs',
                    'racks' => [],
                    'pods' => [],
                    'walls' => $data['jsondata']['walls'],
                    'footprints' => [],
                    'circuits' => [],
                    'pillars' => $data['jsondata']['pillars'],
                ],
                [
                    'id' => 2,
                    'name' => 'Circuits électriques',
                    'racks' => [],
                    'pods' => [],
                    'walls' => $data['jsondata']['walls'],
                    'footprints' => [],
                    'circuits' => $data['circuits'],
                    'pillars' => [],
                ],
                [
                    'id' => 3,
                    'name' => 'Surfaces au sol',
                    'racks' => [],
                    'pods' => [],
                    'walls' => $data['jsondata']['walls'],
                    'footprints' => $data['footprints'],
                    'circuits' => [],
                    'pillars' => [],
                ],
                [
                    'id' => 4,
                    'name' => 'Baies',
                    'racks' => $data['racks'],
                    'pods' => [],
                    'walls' => $data['jsondata']['walls'],
                    'footprints' => [],
                    'circuits' => [],
                    'pillars' => [],
                ]
            ]
        ];
//        return $data;
    }

    /**
     * @throws CoreException
     * @throws MySQLException
     * @throws CoreUnexpectedValue
     * @throws OQLException
     * @throws ArchivedObjectException
     */
    private static function onSuccess(Response $r): void
    {
        if (!UserRights::IsLoggedIn()) {
            throw new RuntimeException('Not authenticated', 401);
        }

        $iLocationId = static::$locationId;
        if ($iLocationId <= 0) {
            throw new RuntimeException('Missing/invalid location_id', 400);
        }

        // Récupère l’objet Location
        $oLocation = (new Locations())->getFromId($iLocationId);
        if ($oLocation === null) {
            throw new RuntimeException('Location not found', 404);
        }

        $r->success(static::formatData([
            'id' => $oLocation->GetKey(),
            'name' => $oLocation->GetName(),
            'friendlyname' => $oLocation->Get('friendlyname'),
            'status' => $oLocation->Get('status'),
            'jsondata' => (new FloorPlanBuilder())->getFromLocation($iLocationId)[0]['jsondata'],
            'racks' => (new Racks())->getFromLocation($iLocationId),
            'footprints' => (new Footprints())->getFromLocation($iLocationId),
            'circuits' => (new CircuitElecs())->getFromLocation($iLocationId)
        ]));
    }

    private static function onError(Exception $e, Response $r): void
    {
        $r->error($e);
    }

    /**
     * @throws Exception
     */
    public static function main(Response $response): void
    {
        if (static::initialize()) {
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        try {
            static::onSuccess($response);
        }
        catch (Exception $e) {
            static::onError($e, $response);
        }
    }
}

Main::main(new Response());