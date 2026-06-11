<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2013 XXXXX
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
Dict::Add ( 'EN US', 'English', 'English', array (
		'Class:Rack/Attribute:customer_id' => 'Rack\'s User',
		'Class:Rack/Attribute:pod_id' => 'POD',
		//'Class:Rack/Name' => '%1$s - %2$s - %3$s - %4$s',
		'Class:PhysicalDevice/Attribute:location_id' => 'Location',
		'Class:PhysicalDevice/Attribute:location_name' => 'Location Name',
		'Class:Rack/Attribute:PDU_list' => 'PDU list',
		'Class:Rack/Attribute:redundancy_PDU' => 'High availability',
		'Class:Rack/Attribute:redundancy_PDU/disabled' => 'Power Distribution is up if all power sources are up',
		'Class:Rack/Attribute:redundancy_PDU/count' => 'Power Distribution is up if at least %1$s power sources are up',
		'Class:Rack/Attribute:redundancy_PDU/percent' => 'Power Distribution is up if at least %1$s %% of power sources are up',
) );
?>
