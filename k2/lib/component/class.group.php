<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class ComponentGroup
{
	function Rows()
	{
    	global $DB;
    	$arRows = $DB->Rows("SELECT * FROM `k2_component_group` ORDER BY `ID` ASC");
		return $arRows;
	}

	function ID($nID)
	{
		global $LIB, $DB;
		if($arGroup = $DB->Row("SELECT * FROM `k2_component_group` WHERE `ID` = '".$nID."'")){
			return $arGroup;
        }
        $this->Error = Lang('GROUP_NOT_FOUND');
		return false;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;
		if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }

        if($DB->Row("SELECT * FROM `k2_component_group` WHERE `NAME` = '".DBS($arPar['NAME'])."'")){        	$this->Error = Lang('THIS_GROUP_ALREADY_EXISTS');
			return false;
        }

		if($nID = $DB->Insert("
		INSERT INTO `k2_component_group` (
			`NAME`
		)VALUES(
			'".DBS($arPar['NAME'])."'
		);
		")){			return $nID;
		}

    	return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;
        if(!$arGroup = $this->ID($nID)){
        	return false;
        }

        if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }

        if($DB->Row("SELECT * FROM `k2_component_group` WHERE `NAME` = '".DBS($arPar['NAME'])."' AND `ID` != '".$nID."'")){
        	$this->Error = Lang('THIS_GROUP_ALREADY_EXISTS');
			return false;
        }

        if($DB->Query("UPDATE k2_component_group
        SET
			`NAME` = '".DBS($arPar['NAME'])."'
        WHERE
        	`ID` = '".$nID."';
        ")){
	        return true;
        }

    	return false;
	}

	function Delete($nID)
	{
    	global $LIB, $DB;

        if(!$arGroup = $this->ID($nID)){
        	return false;
        }
        if($LIB['COMPONENT']->Rows($nID)){
			$this->Error = Lang('CAN_NOT_BE_REMOVED_GROUP');
        	return false;
        }
        $DB->Query("DELETE FROM `k2_component_group` WHERE `ID` = '".$nID."'");

		return true;
	}
}
?>