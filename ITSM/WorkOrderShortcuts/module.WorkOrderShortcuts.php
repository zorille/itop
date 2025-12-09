<?php
//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'WorkOrderShortcuts/',
	array(
		// Identification
		//
		'label' => 'Ajout des raccourcis WorkOrders',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'itop-welcome-itil/2.4.0',
			'itop-config-mgmt/2.4.0',
			'itop-tickets/2.4.0',
            'itop-service-mgmt/2.0.0||itop-service-mgmt-provider/2.0.0',
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.WorkOrderShortcuts.php',
		),
		'webservice' => array(
			
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
		),
	)
);


?>
