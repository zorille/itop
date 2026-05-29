<?php
//
// iTop module definition file
//

//TODO réinstall + gérer Get & Put

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'FloorPlanBuilder/1.0.0',
	array(
		// Identification
		//
		'label' => 'FloorPlanBuilder Definition',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'itop-datacenter-mgmt/2.0.0',
			'Electricite/3.0.0',
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'console/applicationuiextension.class.inc.php',
			'model.FloorPlanBuilder.php',
		),
		'webservice' => array(
			'pages/webservice.php'
		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any 

		// Default settings
		//
		'settings' => array(
			// Module specific settings go here, if any
			'enabled' => true,
			'debug' => false,
			'footprint_colors' => [
				'#FF6B6B', '#4ECDC4',
				'#45B7D1', '#FFA07A',
				'#98D8C8', '#F06292',
				'#AED581', '#FFD54F',
				'#4DB6AC', '#BA68C8'
			]
		),
	)
);


?>
