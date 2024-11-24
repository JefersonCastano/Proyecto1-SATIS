<?php

namespace Utils;

use MetaModel;
use DBObjectSearch;
use DBObjectSet;
use utils;

class QueryHelper
{
    public static function ExecuteQuery($sQuery, $aExtraParams = array())
	{
        // First perform the query - if the OQL is not ok, it will generate an exception : no need to go further
		if (isset($aExtraParams['query_params'])) {
			$aQueryParams = $aExtraParams['query_params'];
		} elseif (isset($aExtraParams['this->class']) && isset($aExtraParams['this->id'])) {
			$oObj = MetaModel::GetObject($aExtraParams['this->class'], $aExtraParams['this->id']);
			$aQueryParams = $oObj->ToArgsForQuery();
		} else {
			$aQueryParams = array();
		}
		$oFilter = DBObjectSearch::FromOQL($sQuery, $aQueryParams);
		$oFilter->SetShowObsoleteData(utils::ShowObsoleteData());

		$oSet = new DBObjectSet($oFilter);
		return $oSet;
	}

    public static function TransformSecondsToTime($timeSpent)
    {
        $days = floor($timeSpent / 86400);
        $hours = floor(($timeSpent % 86400) / 3600);
        $minutes = floor(($timeSpent % 3600) / 60);
        $seconds = $timeSpent % 60;

        return sprintf('%02dd %02dh %02dmin %02ds', $days, $hours, $minutes, $seconds);
    }
}