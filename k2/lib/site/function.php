<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

function siteName($nID)
{
	global $DB;
	if($arRow = $DB->Rows("SELECT NAME, DOMAIN FROM `k2_site` WHERE ID = '".(int)$nID."'")){
		return ($arRows['DOMAIN']?$arRows['DOMAIN']:$arRows['NAME']);
	}
	return false;
}
?>