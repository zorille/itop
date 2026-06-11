<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2013 XXXXX
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
Dict::Add('FR FR', 'French', 'Français', array(
		'Class:Rack/Attribute:customer_id' => 'Utilisateur de la baie',
		'Class:Rack/Attribute:pod_id' => 'POD',
		//'Class:Rack/Name' => '%1$s - %2$s - %3$s - %4$s',
		'Class:Rack/Attribute:PDU_list' => 'Liste de PDU',
		//Ajout pour renommer la version francaise au lieu de site
		'Class:PhysicalDevice/Attribute:location_id' => 'Lieux',
		'Class:PhysicalDevice/Attribute:location_name' => 'Nom Lieux',
		'Class:PowerDistributionUnit/Attribute:redundancy_PDU' => 'Haute disponibilité électrique',
		'Class:PowerDistributionUnit/Attribute:redundancy_PDU/disabled' => 'Le Rack est opérationnel si toutes les alimentations qui le composent sont opérationnels',
		'Class:PowerDistributionUnit/Attribute:redundancy_PDU/count' => 'Nombre minimal d\'alimentations pour que le Rack soit opérationnel : %1$s',
		'Class:PowerDistributionUnit/Attribute:redundancy_PDU/percent' => 'Pourcentage minimal d\'alimentations pour que le Rack soit opérationnel : %1$s %%',
));
?>
