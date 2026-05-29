<?php
namespace FloorPlanBuilder\webservice;

use NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes\Response;
use RuntimeException;
use utils;

require_once dirname(__FILE__).'/../common/classes.php';

$sOp = utils::ReadParam('op', '', false, 'raw_data');
if ($sOp !== 'locations') {
    return;
}

try {
    if (!file_exists(dirname(__FILE__) . "/webservice/" . strtolower($_SERVER['REQUEST_METHOD']) . ".php")) {
        throw new RuntimeException('Method not allowed', 405);
    }
    require_once dirname(__FILE__) . "/webservice/" . strtolower($_SERVER['REQUEST_METHOD']) . ".php";

    $class = "\FloorPlanBuilder\webservice\webservice\\Main" . ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
    $class::main(new Response());
} catch (RuntimeException $e) {
    (new Response())->error($e);
    exit;
}