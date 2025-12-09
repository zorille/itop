<?php
require_once(APPROOT.'collectors/iTopPersonLDAPCollector.class.inc.php');
require_once(APPROOT.'collectors/iTopUserLDAPCollector.class.inc.php');

//require_once APPROOT . 'collectors/ConnexionManager.class.php';
//
//$connexion = new ConnexionManager($argv);
//$connexion->LoadData();

//require_once APPROOT .'/collectors/includes.php';

Orchestrator::AddRequirement('1.0.0', 'ldap'); // LDAP support is required to run this collector

if (Utils::GetConfigurationValue('collect_person_only', 'yes') == 'yes')
{
	Orchestrator::AddCollector(1, 'iTopPersonLDAPCollector');
}
elseif (Utils::GetConfigurationValue('collect_user_only', 'yes') == 'yes')
{
    Orchestrator::AddCollector(1, 'iTopUserLDAPCollector');
}
else
{
	$iRank = 1;
	Orchestrator::AddCollector($iRank++, 'iTopPersonLDAPCollector');
	Orchestrator::AddCollector($iRank++, 'iTopUserLDAPCollector');
}



