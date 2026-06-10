<?php

SetupWebPage::AddModule(
	__FILE__,
	'DatacentrePowerManagement/1.0.0',
	array(
		'label' => 'Datacentre Power Management',
		'category' => 'business',
		'dependencies' => array(
			'itop-config-mgmt/2.0.0',
			'itop-service-mgmt/2.0.0 || itop-profiles-itil/2.0.0',
			'EnterpriseRackUser/1.0.1',
		),
		'mandatory' => false,
		'visible' => true,
		'datamodel' => array(
		),
		'webservice' => array(),
		'data.struct' => array(),
		'data.sample' => array(),
		'doc.manual_setup' => '',
		'doc.more_information' => '',
		'settings' => array(),
	)
);
