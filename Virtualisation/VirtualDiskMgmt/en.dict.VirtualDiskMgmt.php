<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2013 XXXXX
 * @license     http://opensource.org/licenses/AGPL-3.0
 */
Dict::Add ( 'EN US', 'English', 'English', array (
		// Dictionary entries go here
		'Class:VMVirtualDisk' => 'Virtual Disk',
		'Class:VMVirtualDisk+' => 'Virtual Disk attached to a VM',
		'Class:VMVirtualDisk/Attribute:size' => 'Size in KB',
		'Class:VMVirtualDisk/Attribute:type' => 'Disk Type',
		'Class:VMVirtualDisk/Attribute:type/Value:disk' => 'Disk',
		'Class:VMVirtualDisk/Attribute:type/Value:snapshot' => 'Snapshot',
		'Class:VMVirtualDisk/Attribute:virtualmachine_id' => 'Virtual Machine',
		'Class:VMVirtualDisk/Attribute:logicalvolume_id' => 'Logical Volume',
		'Class:VMVirtualDisk/Attribute:system_id+' => 'Logical Volume based on LUN',
		'Class:VirtualMachine/Attribute:vmvirtualdisk_list' => 'Virtual Disks Attached',
		'Class:VirtualMachine/Attribute:vmvirtualdisk_list+' => '',
		'Class:LogicalVolume/Attribute:vmvirtualdisk_list' => 'Virtual Disks stored',
		'Class:LogicalVolume/Attribute:vmvirtualdisk_list+' => '',
		'Class:LogicalVolume/Attribute:org_id' => 'Organization',
		'Class:LogicalVolume/Attribute:org_id+' => ''
) );
?>
