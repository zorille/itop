<?php

class ComputeNextUpdateDate implements iMetricComputer
{
	public static function GetDescription()
	{
		return "Compute the next update date";
	}

	public function ComputeMetric($oObject)
	{
		$sNextUpdateDate = $oObject->Get('next_update');
		if ($sNextUpdateDate != '')
		{
			$iNextUpdateDate = AttributeDateTime::GetAsUnixSeconds($sNextUpdateDate);
			$iDuration = $iNextUpdateDate - time();
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