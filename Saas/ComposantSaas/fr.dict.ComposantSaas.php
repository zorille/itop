<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2013 XXXXX
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
Dict::Add('FR FR', 'French', 'Français', array(
		'Menu:ComposantSaaS' => 'Composant SAAS',
		
		'Class:SaasProvider' => 'Fournisseur de cloud',
		'Class:SaasProvider/Attribute:cloud_url' => 'URL',
		'Class:SaasProvider/Attribute:status' => 'Status',
		'Class:SaasProvider/Attribute:status/Value:implementation' => 'Implémentation',
		'Class:SaasProvider/Attribute:status/Value:production' => 'Production',
		'Class:SaasProvider/Attribute:status/Value:decommission' => 'Décommission',
		'Class:SaasProvider/Attribute:status/Value:obsolete' => 'Obsolète',
		'Class:SaasProvider/Attribute:functionalcis_list' => 'Liste des CIs',
		'Class:SaasProvider/Attribute:businessprocess_list' => 'Processus Business',
		
		'Class:SaasApplication' => 'Application SAAS',
		'Class:SaasApplication/Attribute:saas_url' => 'URL',
		'Class:SaasApplication/Attribute:status' => 'Status',
		'Class:SaasApplication/Attribute:status/Value:implementation' => 'Implémentation',
		'Class:SaasApplication/Attribute:status/Value:production' => 'Production',
		'Class:SaasApplication/Attribute:status/Value:decommission' => 'Décommission',
		'Class:SaasApplication/Attribute:status/Value:obsolete' => 'Obsolète',
		'Class:SaasApplication/Attribute:saasinteractions_list' => 'Liste des Interactions',
		'Class:SaasApplication/Attribute:saastokens_list' => 'Liste des Tokens',
		
		'Class:SaasInteraction' => 'Interaction Applicative',
		'Class:SaasInteraction/Attribute:applications_list' => 'Liste des Applications',
		'Class:SaasInteraction/Attribute:status' => 'Status',
		'Class:SaasInteraction/Attribute:status/Value:implementation' => 'Implémentation',
		'Class:SaasInteraction/Attribute:status/Value:production' => 'Production',
		'Class:SaasInteraction/Attribute:status/Value:decommission' => 'Décommission',
		'Class:SaasInteraction/Attribute:status/Value:obsolete' => 'Obsolète',
		'Class:SaasInteraction/Attribute:type_connexion' => 'Type de connexion',
		'Class:SaasInteraction/Attribute:saastokens_list' => 'Liste des Tokens',
		'Class:SaasInteraction/Attribute:businessprocess_list' => 'Processus Business',
		
		'Class:SaasToken' => 'Token Applicatif',
		'Class:SaasToken/Attribute:application_id' => 'Application',
		'Class:SaasToken/Attribute:application_id+' => 'Application qui contient le token',
		'Class:SaasToken/Attribute:token_id' => 'Id du token',
		'Class:SaasToken/Attribute:status' => 'Status',
		'Class:SaasToken/Attribute:status/Value:active' => 'Actif',
		'Class:SaasToken/Attribute:status/Value:inactive' => 'Inactif',
		'Class:SaasToken/Attribute:expiration_date' => 'Date d\'expiration',
		'Class:SaasToken/Attribute:type_connexion' => 'Type de token',
		'Class:SaasToken/Attribute:status/Value:oauth2' => 'OAuth2',
		'Class:SaasToken/Attribute:status/Value:sso' => 'SSO',
		'Class:SaasToken/Attribute:status/Value:pwd' => 'Utilisateur/Mot de passe',
		'Class:SaasToken/Attribute:interactions_list' => 'Liste des Interactions',
		
		'Class:lnkApplicationToSaasInteraction' => 'Lien Applicatif Interaction',
		'Class:lnkApplicationToSaasInteraction/Attribute:saasinteraction_id' => 'Interaction Applicative',
		'Class:lnkApplicationToSaasInteraction/Attribute:saasapplication_id' => 'Application',
		'Class:lnkApplicationToSaasInteraction/Attribute:role' => 'Postionnement',
		'Class:lnkApplicationToSaasInteraction/Attribute:role+' => 'Postionnement de l\'application dans l\'interaction : Émeteur/Source ou Récepteur/Destination',
		'Class:lnkApplicationToSaasInteraction/Attribute:role/Value:source' => 'Émeteur',
		'Class:lnkApplicationToSaasInteraction/Attribute:role/Value:destination' => 'Récepteur',
		'Class:lnkApplicationToSaasInteraction/Attribute:saastoken_id' => 'Token utilisé',
		
		'Class:lnkSaasTokenToSaasInteraction' => 'Lien Interaction Token',
		'Class:lnkSaasTokenToSaasInteraction/Attribute:saasinteraction_id' => 'Interaction Applicative',
		'Class:lnkSaasTokenToSaasInteraction/Attribute:saastoken_id' => 'Token Applicatif',
		
		'Class:lnkSaasInteractionToBusinessProcess' => 'Lien Interaction Business Processus',
		'Class:lnkSaasInteractionToBusinessProcess/Attribute:saasinteraction_id' => 'Interaction Applicative',
		'Class:lnkSaasInteractionToBusinessProcess/Attribute:businessprocess_id' => 'Processus Business',
		
		'Class:SoftwareInstance/Attribute:saasinteractions_list' => 'Liste des Interactions',
));
?>
