<?php

SetupWebPage::AddModule(
	__FILE__,
	'AuthTokenClient/1.0.0',
	array(
		'label' => 'Auth Token Client',
		'category' => 'business',
		'dependencies' => array(
			'itop-attribute-encrypted-password/1.0.0',
		),
		'mandatory' => false,
		'visible' => true,
		'datamodel' => array(
			'wsclient.class.php',
		),
		'webservice' => array(),
		'data.struct' => array(),
		'data.sample' => array(),
		'doc.manual_setup' => '',
		'doc.more_information' => '',
		'settings' => array(),
	)
);
