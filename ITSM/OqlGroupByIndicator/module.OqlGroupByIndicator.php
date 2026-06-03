<?php

SetupWebPage::AddModule(
	__FILE__,
	'OqlGroupByIndicator/1.0.0',
	array(
		'label' => 'OQL GroupBy Indicator',
		'category' => 'business',
		'dependencies' => array(
			'itop-config-mgmt/2.0.0',
			'itop-tickets/2.0.0',
			'itop-incident-mgmt-itil/2.0.0',
		),
		'mandatory' => false,
		'visible' => true,
		'datamodel' => array(
			'model.oql-groupby-indicator.php',
		),
		'webservice' => array(),
		'dictionary' => array(
			'dictionaries/en.dict.oql-groupby-indicator.php',
			'dictionaries/fr.dict.oql-groupby-indicator.php',
		),
		'data.struct' => array(),
		'data.sample' => array(),
		'doc.manual_setup' => '',
		'doc.more_information' => '',
		'settings' => array(),
	)
);
