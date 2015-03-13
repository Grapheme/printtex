<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

function includeComponent($sText)
{
	global $LIB;
	$sText = $LIB['COMPONENT']->IncludeComponent($sText);	return $sText;
}
?>