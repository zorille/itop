<?php
// Copyright (C) 2014 Combodo SARL
//
//   This application is free software; you can redistribute it and/or modify	
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with this application. If not, see <http://www.gnu.org/licenses/>

require_once(APPROOT.'collectors/rt_classes.php');

$index = 1;

Orchestrator::AddCollector($index++, 'RTLocationCollector');
Orchestrator::AddCollector($index++, 'RTBrandCollector');
Orchestrator::AddCollector($index++, 'RTModelCollector');
Orchestrator::AddCollector($index++, 'RTOSFamilyCollector');
Orchestrator::AddCollector($index++, 'RTOSVersionCollector');
//Orchestrator::AddCollector($index++, 'RTOSLicenceCollector');

if (Utils::GetConfigurationValue('NetworkDeviceCollection', 'yes') == 'yes')
{
	Orchestrator::AddCollector($index++, 'RTNetworkDeviceTypeCollector');
	Orchestrator::AddCollector($index++, 'RTIOSVersionCollector');
	Orchestrator::AddCollector($index++, 'RTNetworkDeviceCollector');
//	Orchestrator::AddCollector($index++, 'RTServerPhysicalInterfaceCollector');
}

if (Utils::GetConfigurationValue('RackCollection', 'yes') == 'yes')
{
	Orchestrator::AddCollector($index++, 'RTRackCollector');
//	Orchestrator::AddCollector($index++, 'RTServerPhysicalInterfaceCollector');
}

if (Utils::GetConfigurationValue('ServerCollection', 'yes') == 'yes')
{
	Orchestrator::AddCollector($index++, 'RTServerCollector');
//	Orchestrator::AddCollector($index++, 'RTServerPhysicalInterfaceCollector');
}
if (Utils::GetConfigurationValue('HypervisorCollection', 'yes') == 'yes')
{
	Orchestrator::AddCollector($index++, 'RTFarmCollector');
	Orchestrator::AddCollector($index++, 'RTHypervisorCollector');
}

if (Utils::GetConfigurationValue('VMCollection', 'yes') == 'yes')
{
	Orchestrator::AddCollector($index++, 'RTVirtualMachineCollector');
//	Orchestrator::AddCollector($index++, 'RTLogicalInterfaceCollector');
}

