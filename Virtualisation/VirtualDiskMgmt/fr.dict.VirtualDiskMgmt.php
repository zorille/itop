<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2013 XXXXX
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
Dict::Add ( 'FR FR', 'French', 'Français', array (
		// Dictionary entries go here
		'Class:VMVirtualDisk' => 'Disque Virtuel',
		'Class:VMVirtualDisk+' => 'Disque virtuel atttaché à une VM',
		'Class:VMVirtualDisk/Attribute:size' => 'Taille en KB',
		'Class:VMVirtualDisk/Attribute:type' => 'Type de disque',
		'Class:VMVirtualDisk/Attribute:type/Value:disk' => 'Disque',
		'Class:VMVirtualDisk/Attribute:type/Value:snapshot' => 'Snapshot',
		'Class:VMVirtualDisk/Attribute:virtualmachine_id' => 'Machine Virtuelle',
		'Class:VMVirtualDisk/Attribute:logicalvolume_id' => 'Volume Logique',
		'Class:VMVirtualDisk/Attribute:system_id+' => 'Volume Logique créé sur un SAN/NAS',
		'Class:VirtualMachine/Attribute:vmvirtualdisk_list' => 'Disques rattachés',
		'Class:VirtualMachine/Attribute:vmvirtualdisk_list+' => '',
		'Class:LogicalVolume/Attribute:vmvirtualdisk_list' => 'Disques virtuels stockés',
		'Class:LogicalVolume/Attribute:vmvirtualdisk_list+' => '',
		'Class:LogicalVolume/Attribute:org_id' => 'Organisation',
		'Class:LogicalVolume/Attribute:org_id+' => ''
) );
?>
