<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

if(!$CURRENT){
	exit;
}
if($GLOBALS['NAV']){
	foreach($GLOBALS['NAV'] as $nNav)
	{
		$DELAYED_VARIABLE['NAV'.$nNav] = $LIB['NAV']->BackResult($nNav);
	}
}
?>