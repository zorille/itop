<?php

class ComputeRiskTriggerDate implements iMetricComputer
{
	public static function GetDescription()
	{
		return "Compute the risk trigger date";
	}

	public function ComputeMetric($oObject)
	{
		$sRiskTriggerDate = $oObject->Get('trigger_date');
		if ($sRiskTriggerDate != '')
		{
			$iRiskTriggerDate = AttributeDateTime::GetAsUnixSeconds($sRiskTriggerDate);
			$iDuration = $iRiskTriggerDate - time();
			if ($iDuration > 0)
			{
				if ($iDuration == PHP_INT_MAX)
				{
					return null;
				}
				else
				{
					return $iDuration;
				}
			}
		}

		return null;
	}
}
