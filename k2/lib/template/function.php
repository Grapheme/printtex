<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

function templateInclude($sFile)
{
	global $LIB, $MOD, $MOD_SETTING, $DB, $CURRENT, $USER;

    $sPath = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/';
	if($CURRENT['COMPONENT']['ID']){
		$sPath .= 'component/'.$CURRENT['COMPONENT']['ID'];
	}elseif($CURRENT['BLOCK']['ID']){
		$sPath .= 'block/'.$CURRENT['BLOCK']['ID'];
	}
	$sPath .= '/'.$sFile;
	if(file_exists($sPath)){
		include($sPath);
	}
}
?>