<?php
// Copyright (C) 2010-2018 Combodo SARL
//
//   This program is free software; you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation; version 3 of the License.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License
//   along with this program; if not, write to the Free Software
//   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

/**
 * Module itop-racktable
 *
 * @author      Erwan Taloc <erwan.taloc@combodo.com>
 * @author      Romain Quetiez <romain.quetiez@combodo.com>
 * @author      Denis Flaven <denis.flaven@combodo.com>
 * @license     http://www.opensource.org/licenses/gpl-3.0.html LGPL
 */



class RTNGPCplugin implements iApplicationUIExtension
{
	public function OnDisplayProperties($oObject, WebPage $oPage, $bEditMode = false)
	{
	}

	public function OnDisplayRelations($oObject, WebPage $oPage, $bEditMode = false)
	{
		if ( in_array(get_class($oObject),array('NetworkDevice','Server','VirtualMachine','Farm','Rack','Hypervisor')) )
		{
		        $aSynchroData = $oObject->GetSynchroData();
		        $iRTID = null;
		        foreach($aSynchroData as $iSourceId => $aData)
		        {
		            /**
		             * @var SynchroDataSource $oSynchroDataSource
		             */
		            $oSynchroDataSource = $aData['source'];
		            if (preg_match('/RackTable:(.*)$/', $oSynchroDataSource->GetName()))
		            {
		                /**
		                 * @var SynchroReplica $oReplica
		                 */
		                foreach($aData['replica'] as $oReplica)
		                {
		                    // Ignore non-synchronized replicas
		                    if ($oReplica->Get('status') !== 'synchronized') continue;
		                    
		                    $sSQLTable = $oSynchroDataSource->GetDataTable();
		                    $aExtraData = $oReplica->LoadExtendedDataFromTable($sSQLTable);
		                    // Hack: the RTID is stored in the NON-SYNCHRONIZED field 'tickets_list' !!!
		                    $iRTID = $aExtraData['tickets_list'];
		                    // Stop once we've found one RT ID (we'll display only one iframe)
		                    break;
		                }
		            }
		        }
		        if ( $iRTID !== null )
		        {
					$oPage->SetCurrentTab(Dict::S('RackTable Inventory'));
					$sRacktableURL = MetaModel::GetModuleSetting('itop-racktable', 'racktable_url', '');
					$sHostURL = $sRacktableURL.'index.php?page=object&object_id='.$iRTID;
					$oPage->add("<div id=\"racktable\" class=\"resizable\" style=\"height:1000px;\"><iframe src=\"$sHostURL\" style=\"width:100%;height:100%;\"></iframe></div>");
		        }
		}
	}

	public function OnFormSubmit($oObject, $sFormPrefix = '')
	{
	}

	public function OnFormCancel($sTempId)
	{
	}

	public function EnumUsedAttributes($oObject)
	{
		return array();
	}

	public function GetIcon($oObject)
	{
		return '';
	}

	public function GetHilightClass($oObject)
	{
		// Possible return values are:
		// HILIGHT_CLASS_CRITICAL, HILIGHT_CLASS_WARNING, HILIGHT_CLASS_OK, HILIGHT_CLASS_NONE	
		return HILIGHT_CLASS_NONE;
	}

	public function EnumAllowedActions(DBObjectSet $oSet)
	{

		return array();
	}

}

