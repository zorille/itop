<?php

SetupWebPage::AddModule(
	__FILE__,
	'EnterpriseEVoViewInRack/1.0.0',
	array(
		'label' => 'Add EVO Dataviz view in Rack',
		'category' => 'business',
		'dependencies' => array(
			'itop-config-mgmt/2.0.0',
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
