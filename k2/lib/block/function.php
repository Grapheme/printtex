<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

function blockName($nID)
{
	if($arRow = $DB->Row("SELECT `NAME` FROM `k2_block` WHERE `ID` = '".(int)$nID."'")){
	}
	return false;
}
?>