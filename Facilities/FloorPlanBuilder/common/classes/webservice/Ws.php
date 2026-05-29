<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\webservice;

use Exception;
use LoginWebPage;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\FromItopParam;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\FromRequestBody;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\Instance;
use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\Response;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

abstract class Ws
{
    /**
     * @throws ReflectionException
     */
    protected static function initializeAttributes(): void
    {
        $ref = new ReflectionClass(static::class);
        $props = $ref->getProperties();
        foreach ($props as $prop) {
            $attrs = $prop->getAttributes(FromItopParam::class);
            if (count($attrs) > 0) {
                /** @var FromItopParam $instance */
                $instance = $attrs[0]->newInstance();
                $instance->setClass(static::class)->setValue($prop->getName());
            }

            $attrs = $prop->getAttributes(FromRequestBody::class);
            if (count($attrs) > 0) {
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    throw new RuntimeException("GET method must not have restes body", 400);
                }
                /** @var FromRequestBody $instance */
                $instance = $attrs[0]->newInstance();
                $instance->setClass(static::class)->setValue($prop->getName());
            }

            $attrs = $prop->getAttributes(Instance::class);
            if (count($attrs) > 0) {
                /** @var Instance $instance */
                $instance = $attrs[0]->newInstance();
                $instance->setValue2($prop);
            }
        }
    }

    /**
     * @throws Exception
     */
    protected static function manageLogin(): void
    {
        // ✅ IMPORTANT: initialise l'auth iTop (session + user) pour une page custom
        require_once APPROOT.'/application/loginwebpage.class.inc.php';
        // backoffice si ta page est dans la console admin, sinon null pour "n'importe quel GUI"
        LoginWebPage::DoLoginEx('backoffice', false);
    }

    /**
     * @throws Exception
     */
    private static function initialize(): void
    {
        require_once APPROOT.'/approot.inc.php';
        require_once APPROOT.'/application/startup.inc.php';

        static::manageLogin();

        static::initializeAttributes();

    }

    abstract protected static function onSuccess(Response $r): void;

    private static function onError(Exception $e, Response $r): void
    {
        $r->error($e);
    }

    /**
     * @throws Exception
     */
    public static function main(Response $r): void
    {
        static::initialize();

        header('Content-Type: application/json; charset=utf-8');

        try {
            static::onSuccess($r);
        }
        catch (Exception $e) {
            static::onError($e, $r);
        }
    }
}