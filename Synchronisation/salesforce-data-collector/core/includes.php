<?php
require_once APPROOT . 'core/parameters.class.inc.php';
require_once APPROOT . 'core/utils.class.inc.php';
require_once APPROOT . 'core/restclient.class.inc.php';
require_once APPROOT . 'core/lookuptable.class.inc.php';
require_once APPROOT . 'core/mappingtable.class.inc.php';
require_once APPROOT . 'core/collector.class.inc.php';
require_once APPROOT . 'core/orchestrator.class.inc.php';
require_once APPROOT . 'core/sqlcollector.class.inc.php';
// Depends on Orchestrator for settings a minimum version for PHP because of the use of PDO