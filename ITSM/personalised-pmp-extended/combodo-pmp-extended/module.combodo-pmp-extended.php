<?php
//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'combodo-pmp-extended/1.0.4',
	array(
		// Identification
		//
		'label' => 'Project Management extended module',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'combodo-pmp-light/1.0.4',
			'itop-change-mgmt/2.0.0||itop-change-mgmt-itil/2.0.0',
		),
		'mandatory' => false,
		'visible' => true,
		'installer' => 'PMPExtendedInstaller',


		// Components
		//
		'datamodel' => array(
			'model.combodo-pmp-extended.php',
			'src/Model/ComputeNextUpdateDate.php',
			'src/Model/ComputeRiskTriggerDate.php',
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
if (!class_exists('PMPExtendedInstaller')) {
	// Module installation handler
	//
	class PMPExtendedInstaller extends ModuleInstallerAPI
	{
		/**
		 * @inheritdoc
		 */
		public static function BeforeWritingConfig(Config $oConfiguration)
		{
			// Rule to add to the object copier configuration
			//duplicate a project with delivrable
			$aNewRule = array(
				'id' => 'CopyProjectExt',
				'source_scope' => 'SELECT Project WHERE status NOT IN (\'closed\',\'monitored\')',
				'allowed_profiles' => 'Project Manager,Administrator',
				'menu_label' => Dict::s('Class:Project/DuplicateProject'),
				'form_label' => Dict::s('Class:Project/DuplicateProjectForm'),
				'report_label' => Dict::s('Class:Project/ReportLabel'),
				'dest_class' => 'Project',
				'preset' =>
					array(
						0 => 'clone_scalars(*)',
						1 => 'clone(contacts_list,risks_list,projectchanges_list,functionalcis_list)',
						2 => 'call_method(Copydeliverablestodeliverables)',
					),
				'retrofit' =>
					array(),
			);

			// Retrieving object copier rules from conf parameters
			// Note: We don't do anything if object copier is not installed, otherwise its configuration will be set when installed.
			$aExistingRules = $oConfiguration->GetModuleSetting('itop-object-copier', 'rules', array());
			if (!empty($aExistingRules)) {
				$bFound = false;
				foreach ($aExistingRules as $iKey => $aExistingRule) {
					if (isset($aExistingRule['menu_label']) && ($aExistingRule['menu_label'] === $aNewRule['menu_label'])) {
						$bFound = true;
						if ($aExistingRule["id"] == 'CopyProjectLight') {
							$aExistingRules[$iKey] = $aNewRule;
							$oConfiguration->SetModuleSetting('itop-object-copier', 'rules', $aExistingRules);
						}
						break;
					}
				}

				// Add rule only if not already existing
				if ($bFound === false) {
					$aExistingRules[] = $aNewRule;
					$oConfiguration->SetModuleSetting('itop-object-copier', 'rules', $aExistingRules);
				}
			}

			return $oConfiguration;
		}
	}
}