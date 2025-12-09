<?php
// Copyright (C) 2018 Combodo SARL
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

class RTLocationCollector extends SQLCollector
{
}

class RTBrandCollector extends SQLCollector
{
}

class RTModelCollector extends SQLCollector
{
}

class RTOSFamilyCollector extends SQLCollector
{
}
class RTOSVersionCollector extends SQLCollector
{
}
//class RTOSLicenceCollector extends SQLCollector
//{
//}

class RTNetworkDeviceTypeCollector extends SQLCollector
{
}

class RTIOSVersionCollector extends SQLCollector
{
}

class RTNetworkDeviceCollector extends SQLCollector
{
    protected $oModelLookup;
	
	public function AttributeIsOptional($sAttCode)
    {
        // If the module Service Management for Service Providers is selected during the setup
        // there is no "services_list" attribute on VirtualMachines. Let's safely ignore it.
        if ($sAttCode == 'services_list') return true;
        if ($sAttCode == 'enclosure_id') return true;
        if ($sAttCode == 'powerA_id') return true;
        if ($sAttCode == 'powerB_id') return true;
        
        // Backward comptability with previous versions which were adding an RTid field
        if ($sAttCode == 'RTid') return true;
        
        // For information
        if ($sAttCode == 'tickets_list')
        {
            Utils::Log(LOG_INFO, "[".__CLASS__."] The column tickets_list is used for storing the RT ID in order to display the RT tab on Servers. You can safely ignore the warning about it.");
        }
        
        return parent::AttributeIsOptional($sAttCode);
    }
    
    protected function MustProcessBeforeSynchro()
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and OSFamily/OSVersion in iTop
        return true;
    }
    
    protected function InitProcessBeforeSynchro()
    {
        // Retrieve the identifiers of the Model since we must do a lookup based on two fields: Brand + Model
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oModelLookup = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'), false /* non-case sensitive */);
    }
    
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        $this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);
    }
}

class RTRackCollector extends SQLCollector
{
    public function AttributeIsOptional($sAttCode)
    {
        // If the module Service Management for Service Providers is selected during the setup
        // there is no "services_list" attribute on VirtualMachines. Let's safely ignore it.
        if ($sAttCode == 'services_list') return true;
        
        // For information
        if ($sAttCode == 'tickets_list')
        {
            Utils::Log(LOG_INFO, "[".__CLASS__."] The column tickets_list is ignored. You can safely ignore the warning about it.");
        }
        
        return parent::AttributeIsOptional($sAttCode);
    }
    
    protected function MustProcessBeforeSynchro()
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and SELECT Location in iTop
        //return true;
    }
    
    protected function InitProcessBeforeSynchro()
    {
        // Retrieve the identifiers of the SELECT Location since we must do a lookup based on two fields: Family + Version
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        //$this->oLocationLookup = new LookupTable('SELECT Location', array('location_id', 'name'));
    }
    
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        //$this->oLocationLookup->Lookup($aLineData, array('master','slave'), 'location_id', $iLineIndex);
    }
}

class RTServerCollector extends SQLCollector
{
    protected $oOSVersionLookup;
	protected $oModelLookup;
	protected $oOSLicenceLookup;
	
	public function AttributeIsOptional($sAttCode)
    {
        // If the module Service Management for Service Providers is selected during the setup
        // there is no "services_list" attribute on VirtualMachines. Let's safely ignore it.
        if ($sAttCode == 'services_list') return true;
        if ($sAttCode == 'enclosure_id') return true;
        if ($sAttCode == 'powerA_id') return true;
        if ($sAttCode == 'powerB_id') return true;
        
        // Backward comptability with previous versions which were adding an RTid field
        if ($sAttCode == 'RTid') return true;
        
        // For information
        if ($sAttCode == 'tickets_list')
        {
            Utils::Log(LOG_INFO, "[".__CLASS__."] The column tickets_list is used for storing the RT ID in order to display the RT tab on Servers. You can safely ignore the warning about it.");
        }
        
        return parent::AttributeIsOptional($sAttCode);
    }
    
    protected function MustProcessBeforeSynchro()
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and OSFamily/OSVersion in iTop
        return true;
    }
    
    protected function InitProcessBeforeSynchro()
    {
        // Retrieve the identifiers of the OSVersion since we must do a lookup based on two fields: Family + Version
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));
        
        // Retrieve the identifiers of the Model since we must do a lookup based on two fields: Brand + Model
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oModelLookup = new LookupTable('SELECT Model', array('brand_id_friendlyname', 'name'), false /* non-case sensitive */);

        $this->oOSLicenceLookup = new LookupTable('SELECT OSLicence', array('osversion_id', 'name'));        
    }
    
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        $this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
        $this->oModelLookup->Lookup($aLineData, array('brand_id', 'model_id'), 'model_id', $iLineIndex);
        $this->oOSLicenceLookup->Lookup($aLineData, array('osversion_id', 'oslicence_id'), 'oslicence_id', $iLineIndex);
    }
}

class RTFarmCollector extends SQLCollector
{
}

class RTHypervisorCollector extends SQLCollector
{
    public function AttributeIsOptional($sAttCode)
    {
        // If the module Service Management for Service Providers is selected during the setup
        // there is no "services_list" attribute on VirtualMachines. Let's safely ignore it.
        if ($sAttCode == 'services_list') return true;
        
        // For information
        if ($sAttCode == 'tickets_list')
        {
            Utils::Log(LOG_INFO, "[".__CLASS__."] The column tickets_list is ignored. You can safely ignore the warning about it.");
        }
        
        return parent::AttributeIsOptional($sAttCode);
    }
    
    protected function MustProcessBeforeSynchro()
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and SELECT Location in iTop
        //return true;
    }
    
    protected function InitProcessBeforeSynchro()
    {
        // Retrieve the identifiers of the SELECT Location since we must do a lookup based on two fields: Family + Version
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        //$this->oLocationLookup = new LookupTable('SELECT Location', array('location_id', 'name'));
    }
    
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        //$this->oLocationLookup->Lookup($aLineData, array('master','slave'), 'location_id', $iLineIndex);
    }
}

//class RTServerPhysicalInterfaceCollector extends SQLCollector
//{
//}

//class RTPCModelCollector extends SQLCollector
//{
//}

//class RTPCCollector extends SQLCollector
//{

class RTVirtualMachineCollector extends SQLCollector
{
    protected $oOSVersionLookup;
	protected $oOSLicenceLookup;
	
	public function AttributeIsOptional($sAttCode)
    {
	if ($sAttCode == 'services_list') return true;
        // For backward comptability with previous versions which were adding an RTid field
        if ($sAttCode == 'RTid') return true;
        
        // For information
        if ($sAttCode == 'tickets_list')
        {
            Utils::Log(LOG_INFO, "[".__CLASS__."] The column tickets_list is used for storing the RT ID in order to display the RT tab on Virtual Machines. You can safely ignore the warning about it.");
        }
        
        return parent::AttributeIsOptional($sAttCode);
    }
    
    protected function MustProcessBeforeSynchro()
    {
        // We must reprocess the CSV data obtained from the inventory script
        // to lookup the Brand/Model and OSFamily/OSVersion in iTop
        return true;
    }
    
    protected function InitProcessBeforeSynchro()
    {
        // Retrieve the identifiers of the OSVersion since we must do a lookup based on two fields: Family + Version
        // which is not supported by the iTop Data Synchro... so let's do the job of an ETL
        $this->oOSVersionLookup = new LookupTable('SELECT OSVersion', array('osfamily_id_friendlyname', 'name'));        
        $this->oOSLicenceLookup = new LookupTable('SELECT OSLicence', array('osversion_id', 'name'));        
    }
    
    protected function ProcessLineBeforeSynchro(&$aLineData, $iLineIndex)
    {
        // Process each line of the CSV
        $this->oOSVersionLookup->Lookup($aLineData, array('osfamily_id', 'osversion_id'), 'osversion_id', $iLineIndex);
        $this->oOSLicenceLookup->Lookup($aLineData, array('osversion_id', 'oslicence_id'), 'oslicence_id', $iLineIndex);
    }
}
