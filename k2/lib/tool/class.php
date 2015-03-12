<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class Tool
{	function Delayed($sKey, $sText)
	{
		global $DELAYED_VARIABLE;
		$DELAYED_VARIABLE[$sKey] = $sText;
	}
}
?>