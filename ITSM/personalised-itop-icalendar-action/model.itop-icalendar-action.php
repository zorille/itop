<?php

/**
 * An iCalendar "VEVENT" notification, send as an email
 *
 * @package     iTopORM
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 */
class ActionICalendarEmail extends ActionNotification
{
	public static function Init()
	{
		$aParams = array
		(
			"category" => "core/cmdb,application",
			"key_type" => "autoincrement",
			"name_attcode" => "name",
			"state_attcode" => "",
			"reconc_keys" => array('name'),
			"db_table" => "priv_action_icalendar",
			"db_key_field" => "id",
			"db_finalclass_field" => "",
			"display_template" => "",
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		MetaModel::Init_AddAttribute(new AttributeEmailAddress("test_recipient", array("allowed_values"=>null, "sql"=>"test_recipient", "default_value"=>"", "is_null_allowed"=>true, "depends_on"=>array())));

		MetaModel::Init_AddAttribute(new AttributeString("from", array("allowed_values"=>null, "sql"=>"from", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeString("from_label", array("allowed_values"=>null, "sql"=>"from_label", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeString("reply_to", array("allowed_values"=>null, "sql"=>"reply_to", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeOQL("to", array("allowed_values"=>null, "sql"=>"to", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeOQL("cc", array("allowed_values"=>null, "sql"=>"cc", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeTemplateString("subject", array("allowed_values"=>null, "sql"=>"subject", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeTemplateString("meeting_subject", array("allowed_values"=>null, "sql"=>"meeting_subject", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeTemplateString("meeting_location", array("allowed_values"=>null, "sql"=>"meeting_location", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeTemplateString("start_date", array("allowed_values"=>null, "sql"=>"start_date", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeTemplateString("end_date", array("allowed_values"=>null, "sql"=>"end_date", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeTemplateText("meeting_description", array("allowed_values"=>null, "sql"=>"meeting_description", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		
		// Display lists
		MetaModel::Init_SetZListItems('details', array(
			'col:col0' => array(
				'fieldset:ActionICalendarEmail:Action' => array('name', 'description', 'status'), 
				'fieldset:ActionICalendarEmail:eMail' => array('test_recipient', 'from', 'from_label', 'reply_to', 'to', 'cc', 'subject')
			),
			'col:col1' => array(
				'fieldset:ActionICalendarEmail:Invitation' => array('start_date', 'end_date', 'meeting_subject', 'meeting_description', 'meeting_location')
			),
			'trigger_list')
		); // Attributes to be displayed for the complete details
		MetaModel::Init_SetZListItems('list', array('name', 'status', 'to', 'subject')); // Attributes to be displayed for a list
		// Search criteria
		MetaModel::Init_SetZListItems('standard_search', array('name','description', 'status', 'subject')); // Criteria of the std search form
//		MetaModel::Init_SetZListItems('advanced_search', array('name')); // Criteria of the advanced search form
	}

	// count the recipients found
	protected $m_iRecipients;

	// Errors management : not that simple because we need that function to be
	// executed in the background, while making sure that any issue would be reported clearly
	protected $m_aMailErrors; //array of strings explaining the issue
	
	protected function GetRecipientsSet($sRecipAttCode, $aArgs)
	{
		$sOQL = $this->Get($sRecipAttCode);
		if (strlen($sOQL) == '')
		{
			// empty set
			return DBObjectSet::FromScratch('Person');
		}

		try
		{
			$oSearch = DBObjectSearch::FromOQL($sOQL);
			$oSearch->AllowAllData();
		}
		catch (OQLException $e)
		{
			$this->m_aMailErrors[] = "query syntax error for recipient '$sRecipAttCode'";
			return $e->getMessage();
		}

		$sClass = $oSearch->GetClass();
		// Determine the email attribute (the first one will be our choice)
		foreach (MetaModel::ListAttributeDefs($sClass) as $sAttCode => $oAttDef)
		{
			if ($oAttDef instanceof AttributeEmailAddress)
			{
				$sEmailAttCode = $sAttCode;
				// we've got one, exit the loop
				break;
			}
		}
		if (!isset($sEmailAttCode))
		{
			$this->m_aMailErrors[] = "wrong target for recipient '$sRecipAttCode'";
			return "The objects of the class '$sClass' do not have any email attribute";
		}

		$oSet = new DBObjectSet($oSearch, array() /* order */, $aArgs);
		return $oSet;	
	}

	protected function GetEmailAttCode(DBObjectSet $oSet)
	{
		$oSearch = $oSet->GetFilter(); 
		$sClass = $oSearch->GetClass();
		// Determine the email attribute (the first one will be our choice)
		foreach (MetaModel::ListAttributeDefs($sClass) as $sAttCode => $oAttDef)
		{
			if ($oAttDef instanceof AttributeEmailAddress)
			{
				$sEmailAttCode = $sAttCode;
				// we've got one, exit the loop
				break;
			}
		}
		if (!isset($sEmailAttCode))
		{
			$this->m_aMailErrors[] = "Wrong target for recipient '$sRecipAttCode'"; //FIXME not defined variable
			throw new Exception("The objects of the class '$sClass' do not have any email attribute");
		}
		return $sEmailAttCode;
	}
	
	// returns a the list of emails as a string, or throws an exception
	protected function FindRecipients($sRecipAttCode, $aArgs)
	{
		$aRecipients = array();
		$oSet = $this->GetRecipientsSet($sRecipAttCode, $aArgs);
		$sEmailAttCode = $this->GetEmailAttCode($oSet);
		
		while ($oObj = $oSet->Fetch())
		{
			$sAddress = trim($oObj->Get($sEmailAttCode));
			if (strlen($sAddress) > 0)
			{
				$aRecipients[] = $sAddress;
				$this->m_iRecipients++;
			}
		}
		return implode(', ', $aRecipients);
	}


	public function DoExecute($oTrigger, $aContextArgs)
	{
		if (MetaModel::IsLogEnabledNotification())
		{
			$oLog = new EventNotificationEmail();
			if ($this->IsBeingTested())
			{
				$oLog->Set('message', 'TEST - Notification sent ('.$this->Get('test_recipient').')');
			}
			else
			{
				$oLog->Set('message', 'Notification pending');
			}
			$oLog->Set('userinfo', UserRights::GetUser());
			$oLog->Set('trigger_id', $oTrigger->GetKey());
			$oLog->Set('action_id', $this->GetKey());
			$oLog->Set('object_id', $aContextArgs['this->object()']->GetKey());
			// Must be inserted now so that it gets a valid id that will make the link
			// between an eventual asynchronous task (queued) and the log
			$oLog->DBInsertNoReload();
		}
		else
		{
			$oLog = null;
		}

		try
		{
			$sRes = $this->_DoExecute($oTrigger, $aContextArgs, $oLog);

			if ($this->IsBeingTested())
			{
				$sPrefix = 'TEST ('.$this->Get('test_recipient').') - ';
			}
			else
			{
				$sPrefix = '';
			}
			$oLog->Set('message', $sPrefix.$sRes);

		}
		catch (Exception $e)
		{
			if ($oLog)
			{
				$oLog->Set('message', 'Error: '.$e->getMessage());
			}
		}
		if ($oLog)
		{
			$oLog->DBUpdate();
		}
	}

	protected function _DoExecute($oTrigger, $aContextArgs, &$oLog)
	{
		$sPreviousUrlMaker = ApplicationContext::SetUrlMakerClass();
		try
		{
			$this->m_iRecipients = 0;
			$this->m_aMailErrors = array();
			$bRes = false; // until we do succeed in sending the email
	
			// Determine recicipients
			//
			$sTo = $this->FindRecipients('to', $aContextArgs);
			$sCC = $this->FindRecipients('cc', $aContextArgs);
	
			$sFrom = MetaModel::ApplyParams($this->Get('from'), $aContextArgs);
			$sReplyTo = MetaModel::ApplyParams($this->Get('reply_to'), $aContextArgs);
	
			$sSubject = MetaModel::ApplyParams($this->Get('subject'), $aContextArgs);
			$sBody = $this->GetBody($aContextArgs);
			
			$oObj = $aContextArgs['this->object()'];
			$sMessageId = sprintf('iTop_%s_%d_%f@%s.openitop.org', get_class($oObj), $oObj->GetKey(), microtime(true /* get as float*/), MetaModel::GetEnvironmentId());
			$sReference = '<'.$sMessageId.'>';
		}
		catch(Exception $e)
		{
  			ApplicationContext::SetUrlMakerClass($sPreviousUrlMaker);
  			throw $e;
  		}
		ApplicationContext::SetUrlMakerClass($sPreviousUrlMaker);
		
		if (!is_null($oLog))
		{
			// Note: we have to secure this because those values are calculated
			// inside the try statement, and we would like to keep track of as
			// many data as we could while some variables may still be undefined
			if (isset($sTo))       $oLog->Set('to', $sTo);
			if (isset($sCC))       $oLog->Set('cc', $sCC);
			if (isset($sFrom))     $oLog->Set('from', $sFrom);
			if (isset($sSubject))  $oLog->Set('subject', $sSubject);
			if (isset($sBody))     $oLog->Set('body', $sBody);
		}
		$sStyles = file_get_contents(APPROOT.'css/email.css');
                $sStyles .= MetaModel::GetConfig()->Get('email_css');

		$oEmail = new EMail();

		if ($this->IsBeingTested())
		{
			$oEmail->SetSubject('TEST['.$sSubject.']');
			$sTestBody = $sBody;
			$sTestBody .= "<div style=\"border: dashed;\">\n";
			$sTestBody .= "<h1>Testing email notification ".$this->GetHyperlink()."</h1>\n";
			$sTestBody .= "<p>The email should be sent with the following properties\n";
			$sTestBody .= "<ul>\n";
			$sTestBody .= "<li>TO: $sTo</li>\n";
			$sTestBody .= "<li>CC: $sCC</li>\n";
			$sTestBody .= "<li>From: $sFrom</li>\n";
			$sTestBody .= "<li>Reply-To: $sReplyTo</li>\n";
			$sTestBody .= "<li>References: $sReference</li>\n";
			$sTestBody .= "</ul>\n";
			$sTestBody .= "</p>\n";
			$sTestBody .= "</div>\n";
			$oEmail->SetBody($sTestBody, 'text/html', $sStyles);
			$oEmail->SetRecipientTO($this->Get('test_recipient'));
			$oEmail->SetRecipientFrom($sFrom);
			$oEmail->SetReferences($sReference);
			$oEmail->SetMessageId($sMessageId);
			$sVEvent = $this->GetICalendarVEVENT($aContextArgs);
			if ($sVEvent != null)
			{
				$oEmail->AddPart($sVEvent, 'text/calendar');
				$oEmail->AddAttachment($sVEvent, 'invite.ics', 'text/calendar');
			}
		}
		else
		{
            $oEmail->SetSubject($sSubject);
            $sDescription = MetaModel::ApplyParams($this->Get('meeting_description'), $aContextArgs);
            $oEmail->SetBody($sDescription, 'text/html', $sStyles);

            $oEmail->SetRecipientTO($sTo);
            $oEmail->SetRecipientCC($sCC);
            $oEmail->SetRecipientFrom($sFrom);
            $oEmail->SetRecipientReplyTo($sReplyTo);
            $oEmail->SetReferences($sReference);
            $oEmail->SetMessageId($sMessageId);
            $sVEvent = $this->GetICalendarVEVENT($aContextArgs);
			if ($sVEvent != null)
			{
                $oEmail->AddPart($sVEvent, 'text/calendar; charset=utf-8; method=REQUEST; name=invite.ics');
			}
		}

		if (isset($aContextArgs['attachments']))
		{
			$aAttachmentReport = array();
			foreach($aContextArgs['attachments'] as $oDocument)
			{
				$oEmail->AddAttachment($oDocument->GetData(), $oDocument->GetFileName(), $oDocument->GetMimeType());
				$aAttachmentReport[] = array($oDocument->GetFileName(), $oDocument->GetMimeType(), strlen($oDocument->GetData()));
			}
			$oLog->Set('attachments', $aAttachmentReport);
		}

		if (empty($this->m_aMailErrors))
		{
			if ($this->m_iRecipients == 0)
			{
				return 'No recipient';
			}
			else
			{
				$iRes = $oEmail->Send($aErrors, false, $oLog); // allow asynchronous mode
				switch ($iRes)
				{
					case EMAIL_SEND_OK:
						return "Sent";
	
					case EMAIL_SEND_PENDING:
						return "Pending";
	
					case EMAIL_SEND_ERROR:
						return "Errors: ".implode(', ', $aErrors);
				}
			}
		}
		else
		{
			if (is_array($this->m_aMailErrors) && count($this->m_aMailErrors) > 0)
			{
				$sError = implode(', ', $this->m_aMailErrors);
			}
			else
			{
				$sError = 'Unknown reason';
			}
			return 'Notification was not sent: '.$sError;
		}
	}

	protected function GetBody($aContextArgs)
	{
		return Dict::S('ActionICalendarEmail:DefaultBody');
	}
	
	protected function GetICalendarVEVENT($aContextArgs)
	{
		$EOL = "\r\n";
		
		// Since the content of the VEVENT is in plain text, let's rebuild the URLs without the <a></a> tag around them
		if (array_key_exists('this->object()', $aContextArgs))
		{
			$aContextArgs['this->hyperlink()'] = ApplicationContext::MakeObjectUrl(get_class($aContextArgs['this->object()']), $aContextArgs['this->object()']->GetKey(), 'iTopStandardURLMaker', false);
			$aContextArgs['this->hyperlink(portal)'] = ApplicationContext::MakeObjectUrl(get_class($aContextArgs['this->object()']), $aContextArgs['this->object()']->GetKey(), 'PortalURLMaker', false);
		}
		
		$sFromAddress = MetaModel::ApplyParams($this->Get('from'), $aContextArgs);
		$sFromLabel = MetaModel::ApplyParams($this->Get('from_label'), $aContextArgs);
		
		$sStart = $this->Get('start_date');
		$sStartTime = MetaModel::ApplyParams($sStart, $aContextArgs);
		$sStartTime = DateTime::createFromFormat(AttributeDateTime::GetFormat(), $sStartTime);
		if (!$sStartTime)
		{
			return null;
		}
		$sStartTime = $sStartTime->getTimestamp();
		$sEndTime = 0;
		if ($this->Get('end_date') != '')
		{
			$sEndTime = MetaModel::ApplyParams($this->Get('end_date'), $aContextArgs);
			$sEndTime = DateTime::createFromFormat(AttributeDateTime::GetFormat(), $sEndTime);
			if (!$sEndTime)
			{
				$sEndTime = 0;
			}
			else
			{
				$sEndTime = $sEndTime->getTimestamp();
			}
		}
		//if the end date is prior to the start date replace it with start time + 1h
		$sEndTime = ($sEndTime < $sStartTime ? $sStartTime + 3600 : $sEndTime);
		
		$sDescription = MetaModel::ApplyParams($this->Get('meeting_description'), $aContextArgs);
		
		$sSubject = MetaModel::ApplyParams($this->Get('subject'), $aContextArgs);
		$sMeetingSubject = MetaModel::ApplyParams($this->Get('meeting_subject'), $aContextArgs);
		if ($sMeetingSubject == '')
		{
			$sMeetingSubject = $sSubject;
		}
		$sLocation = MetaModel::ApplyParams($this->Get('meeting_location'), $aContextArgs);
		
		$sICalEvent = 'BEGIN:VCALENDAR'.$EOL;
		$sICalEvent .= 'PRODID:-//Combodo SARL//iTop//EN'.$EOL;
		$sICalEvent .= 'VERSION:2.0'.$EOL;
		$sICalEvent .= 'METHOD:REQUEST'.$EOL;
		$sICalEvent .= 'BEGIN:VEVENT'.$EOL;
		$sICalEvent .= $this->FoldLine('ORGANIZER;CN="'.$sFromLabel.'":MAILTO:'.$sFromAddress).$EOL;
		$oSet = $this->GetRecipientsSet('to', $aContextArgs);
		$sEmailAttCode = $this->GetEmailAttCode($oSet);
		while($oContact = $oSet->Fetch())
		{
			$sICalEvent .= $this->FoldLine('ATTENDEE;CN="'.$oContact->GetName().'";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:'.$oContact->Get($sEmailAttCode)).$EOL;
		}
		$oSet = $this->GetRecipientsSet('cc', $aContextArgs);
		$sEmailAttCode = $this->GetEmailAttCode($oSet);
		while($oContact = $oSet->Fetch())
		{
			$sICalEvent .= $this->FoldLine('ATTENDEE;CN="'.$oContact->GetName().'";ROLE=OPT-PARTICIPANT;RSVP=TRUE:MAILTO:'.$oContact->Get($sEmailAttCode)).$EOL;
		}
		$sICalEvent .= 'LAST-MODIFIED:' . date("Ymd\TGis").$EOL;
		$sICalEvent .= 'UID:'.date("Ymd\TGis", $sStartTime).rand()."@openitop.org".$EOL;
        $sICalEvent .= 'DTSTAMP:'.gmdate("Ymd\TGis\Z").$EOL;
        $sICalEvent .= 'DTSTART:'.gmdate("Ymd\THis\Z", $sStartTime).$EOL;
        $sICalEvent .= 'DTEND:'.gmdate("Ymd\THis\Z", $sEndTime).$EOL;		
		$sICalEvent .= 'TRANSP:OPAQUE'.$EOL;
		$sICalEvent .= 'SEQUENCE:1'.$EOL;
		$sICalEvent .= $this->FoldLine('SUMMARY:'.$sMeetingSubject).$EOL;
		if ($sLocation != '')
		{
			$sICalEvent .= $this->FoldLine('LOCATION:'.$sLocation).$EOL;
		}
		if ($sDescription != '')
		{
			$sICalEvent .= $this->FoldLine('DESCRIPTION:'.$sDescription).$EOL;
		}
		$sICalEvent .= 'CLASS:PUBLIC'.$EOL;
		$sICalEvent .= 'PRIORITY:5'.$EOL;
		//  The VALARM does not seem to have any effect in Thunderbird and GMail
		$sICalEvent .= 'BEGIN:VALARM'.$EOL;
		$sICalEvent .= 'ACTION:DISPLAY'.$EOL;
		$sICalEvent .= 'DESCRIPTION:Reminder'.$EOL;
		$sICalEvent .= 'TRIGGER:-P0DT0H15M0S'.$EOL;
		$sICalEvent .= 'END:VALARM'.$EOL;
		
		$sICalEvent .= 'END:VEVENT'.$EOL;
		$sICalEvent .= 'END:VCALENDAR'.$EOL;
		
		return $sICalEvent;
	}
	
	protected function FoldLine($sText)
	{
		// Each entry is on one line, so remove the line breaks
		$sLine = str_replace(array("\n", "\r"), " ", $sText);
		// "line" must be folded to 75 characters maximum
		$sLine = chunk_split($sLine, 75, "\r\n "); // folded lines begin with a space
		return $sLine;
	}
}
