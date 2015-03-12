<?
class SEO
{
	function ID($nID)
	{
		global $LIB, $DB;
		if($arPayer = $DB->Row("SELECT * FROM `k2_mod_seo` WHERE `ID` = '".$nID."'")){
			return $arPayer;
        }
        $this->Error = 'Страница не найдена';
		return false;
	}

	function Rows()
	{
    	global $DB;
    	$arPayer = $DB->Rows("SELECT * FROM `k2_mod_seo` ORDER BY `ID` DESC");
		return $arPayer;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;

		if($sError = formCheck(array('PAGE' => 'Путь'))){
       		$this->Error = $sError;
			return false;
        }

        if(preg_match("#http://.*?(/.+)$#", $arPar['PAGE'], $arMath)){
        	$arPar['PAGE'] = $arMath[1];
        }

        if($nID = $DB->Insert("
		INSERT INTO `k2_mod_seo` (
			`PAGE`,
			`TITLE`,
			`KEYWORD`,
			`DESCRIPTION`
		)VALUES(
			'".DBS($arPar['PAGE'])."', '".DBS($arPar['TITLE'])."', '".DBS($arPar['KEYWORD'])."', '".DBS($arPar['DESCRIPTION'])."'
		);")){
        	return $nID;
		}
    	return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;

        if(!$arEmail = $this->ID($nID)){
        	return false;
        }

        if($sError = formCheck(array('PAGE' => 'Путь'))){
       		$this->Error = $sError;
			return false;
        }

        if(preg_match("#http://.*?(/.+)$#", $arPar['PAGE'], $arMath)){        	$arPar['PAGE'] = $arMath[1];
        }

        if($DB->Query("UPDATE `k2_mod_seo`
        SET
			`PAGE` = '".DBS($arPar['PAGE'])."',
			`TITLE` = '".DBS($arPar['TITLE'])."',
			`KEYWORD` = '".DBS($arPar['KEYWORD'])."',
			`DESCRIPTION` = '".DBS($arPar['DESCRIPTION'])."'
        WHERE
        	`ID` = '".$nID."';
        ")){
	        return true;
        }

    	return false;
	}

	function Delete($nID)
	{
    	global $DB;

		$DB->Query("DELETE FROM `k2_mod_seo` WHERE ID = '".(int)$nID."'");

		return true;
	}

	function Set()
	{
    	global $DB, $LIB;

    	if($arRow = $DB->Row("SELECT * FROM `k2_mod_seo` WHERE `PAGE` = '".DBS($_SERVER['REQUEST_URI'])."'")){    		$LIB['TOOL']->Delayed('TITLE', $arRow['TITLE']);
			$LIB['TOOL']->Delayed('KEYWORD', $arRow['KEYWORD']);
			$LIB['TOOL']->Delayed('DESCRIPTION', $arRow['DESCRIPTION']);
    	}
	}
}
?>