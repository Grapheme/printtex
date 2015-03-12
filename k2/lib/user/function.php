<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

function userLogin($nID)
{
	global $DB;
	if($arUser = $DB->Row("SELECT `LOGIN` FROM `k2_user` WHERE `ID` = '".(int)$nID."'")){
		return $arUser['LOGIN'];
	}
}

function userName($nID)
{
	global $DB;
	if($arUser = $DB->Row("SELECT `LOGIN`, `NAME` FROM `k2_user` WHERE `ID` = '".(int)$nID."'")){
		return ($arUser['LOGIN']?$arUser['NAME']:$arUser['LOGIN']);
	}
}

function groupName($nID)
{
	global $DB;
	if($arGroup = $DB->Row("SELECT `NAME` FROM `k2_user_group` WHERE `ID` = '".(int)$nID."'")){
		return $arGroup['NAME'];
	}
}
?>
