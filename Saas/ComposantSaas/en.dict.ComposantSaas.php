<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2013 XXXXX
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
Dict::Add ( 'EN US', 'English', 'English', array (
		'Menu:ComposantSaaS' => 'SAAS Components',
		
		'Class:SaasProvider' => 'Cloud Provider',
		'Class:SaasProvider/Attribute:cloud_url' => 'URL',
		'Class:SaasProvider/Attribute:status' => 'Status',
		'Class:SaasProvider/Attribute:status/Value:implementation' => 'Implementation',
		'Class:SaasProvider/Attribute:status/Value:production' => 'Production',
		'Class:SaasProvider/Attribute:status/Value:decommission' => 'Decommissioning',
		'Class:SaasProvider/Attribute:status/Value:obsolete' => 'Obsolete',
		'Class:SaasProvider/Attribute:functionalcis_list' => 'CI List',
		'Class:SaasProvider/Attribute:businessprocess_list' => 'Business Process',
		
		'Class:SaasApplication' => 'SAAS Application',
		'Class:SaasApplication/Attribute:saas_url' => 'URL',
		'Class:SaasApplication/Attribute:status' => 'Status',
		'Class:SaasApplication/Attribute:status/Value:implementation' => 'Implementation',
		'Class:SaasApplication/Attribute:status/Value:production' => 'Production',
		'Class:SaasApplication/Attribute:status/Value:decommission' => 'Decommissioning',
		'Class:SaasApplication/Attribute:status/Value:obsolete' => 'Obsolete',
		'Class:SaasApplication/Attribute:saasinteractions_list' => 'Interactions List',
		'Class:SaasApplication/Attribute:saastokens_list' => 'Tokens List',
		
		'Class:SaasInteraction' => 'Application Interaction',
		'Class:SaasInteraction/Attribute:applications_list' => 'Applications List',
		'Class:SaasInteraction/Attribute:status' => 'Status',
		'Class:SaasInteraction/Attribute:status/Value:implementation' => 'Implementation',
		'Class:SaasInteraction/Attribute:status/Value:production' => 'Production',
		'Class:SaasInteraction/Attribute:status/Value:decommission' => 'Decommissioning',
		'Class:SaasInteraction/Attribute:status/Value:obsolete' => 'Obsolete',
		'Class:SaasInteraction/Attribute:type_connexion' => 'Connexion type',
		'Class:SaasInteraction/Attribute:saastokens_list' => 'Tokens List',
		'Class:SaasInteraction/Attribute:businessprocess_list' => 'Business Process',
		
		'Class:SaasToken' => 'Application Token',
		'Class:SaasToken/Attribute:application_id' => 'Application',
		'Class:SaasToken/Attribute:application_id+' => 'Application whom contains the token',
		'Class:SaasToken/Attribute:token_id' => 'Token Id',
		'Class:SaasToken/Attribute:status' => 'Status',
		'Class:SaasToken/Attribute:status/Value:active' => 'Active',
		'Class:SaasToken/Attribute:status/Value:inactive' => 'Inactive',
		'Class:SaasToken/Attribute:expiration_date' => 'Expiration Date',
		'Class:SaasToken/Attribute:type_connexion' => 'Token Type',
		'Class:SaasToken/Attribute:status/Value:oauth2' => 'OAuth2',
		'Class:SaasToken/Attribute:status/Value:sso' => 'SSO',
		'Class:SaasToken/Attribute:status/Value:pwd' => 'User/Password',
		'Class:SaasToken/Attribute:interactions_list' => 'Interactions List',
				
		'Class:lnkApplicationToSaasInteraction' => 'Application Interaction Link',
		'Class:lnkApplicationToSaasInteraction/Attribute:saasinteraction_id' => 'Application Interaction',
		'Class:lnkApplicationToSaasInteraction/Attribute:saasapplication_id' => 'Application',
		'Class:lnkApplicationToSaasInteraction/Attribute:role' => 'Interaction Usage',
		'Class:lnkApplicationToSaasInteraction/Attribute:role+' => 'Application Interaction Usage during the interaction: Source or Destination',
		'Class:lnkApplicationToSaasInteraction/Attribute:role/Value:source' => 'Source',
		'Class:lnkApplicationToSaasInteraction/Attribute:role/Value:destination' => 'Destination',
		'Class:lnkApplicationToSaasInteraction/Attribute:saastoken_id' => 'Token used',
		
		'Class:lnkSaasTokenToSaasInteraction' => 'Interaction Token Link',
		'Class:lnkSaasTokenToSaasInteraction/Attribute:saasinteraction_id' => 'Application Interaction',
		'Class:lnkSaasTokenToSaasInteraction/Attribute:saastoken_id' => 'Application Token',
		
		'Class:lnkSaasInteractionToBusinessProcess' => 'Interaction Business Process Link',
		'Class:lnkSaasInteractionToBusinessProcess/Attribute:saasinteraction_id' => 'Application Interaction',
		'Class:lnkSaasInteractionToBusinessProcess/Attribute:businessprocess_id' => 'Business Process',
		
		'Class:SoftwareInstance/Attribute:saasinteractions_list' => 'Interactions List',
) );
?>
