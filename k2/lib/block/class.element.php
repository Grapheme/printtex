<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class BlockElement
{
	function ID($nID, $nSBlock)
	{
		global $DB, $LIB;
		if(!$arSBlock = $LIB['SECTION_BLOCK']->ID($nSBlock)){
			$this->Error = $LIB['SECTION_BLOCK']->Error;
			return false;
		}
		if($arElement = $DB->Row("SELECT * FROM `k2_block".$arSBlock['BLOCK']."` WHERE `ID` = '".(int)$nID."'")){			$arSection = $DB->Row("SELECT `URL` FROM `k2_section` WHERE `ID` = '".$arSBlock['SECTION']."'");
			$arElement['URL'] = $arSection['URL'].$nSBlock.'/'.$arElement['ID'].'/';
			$arElement['URL_BACK'] = $arSection['URL'];

			return $arElement;
		}
        $this->Error = 'Элемент не найден';
        return false;
	}

	function Rows($nBlock, $arFilter = array(), $arOrderBy = array(), $arSelect = array('*'), $arNav = array(), $arLimit = array())
	{        global $LIB, $DB, $USER;

		$nBlock = (int)$nBlock;

		if(!$arSelect){			$arSelect = array('*');
		}elseif($arSelect[0] != '*'){
			$arSelect[] = 'ID';
			$arSelect[] = 'SECTION_BLOCK';
		}

		if(!$arNav['WEIGHT']){
        	$arNav['WEIGHT'] = 9;
        }
        $LIB['NAV']->Setting = array('WEIGHT' => (int)$arNav['WEIGHT'], 'SIZE' => (int)$arNav['SIZE'], 'TOTAL' => 0);

		$arFilter = array(
        'FROM' => 'k2_block'.$nBlock,
        'WHERE' => $arFilter,
        'ORDER_BY' => $arOrderBy,
        'SELECT' => $arSelect,
        'NAV' => $arNav);

        if($arLimit){        	$arFilter['LIMIT'] = $arLimit;
 			$arFilter['NAV'] = false;
 			$LIB['NAV']->Setting = array();
        }

        $sSQL = $DB->CSQL($arFilter);

        if((!$arList = $DB->Rows($sSQL)) && $_GET['page']>1){
        	$_GET['page'] = 1;
        	$sSQL = $DB->CSQL($arFilter);
        	$arList = $DB->Rows($sSQL);
        }
		if($arList){			$arCount = $DB->Row("SELECT FOUND_ROWS()");
			$LIB['NAV']->Setting['TOTAL'] = $arCount['FOUND_ROWS()'];

			$arRows = $DB->Rows("
			SELECT
				SB.ID AS ID,
				S.URL AS URL
			FROM
				k2_section_block AS SB,
				k2_section AS S
			WHERE
				SB.SECTION = S.ID
			");
			for($i=0; $i<count($arRows); $i++)
		    {
		    	$arSBlock[$arRows[$i]['ID']] = $arRows[$i]['URL'];
		    }

            for($i=0; $i<count($arList); $i++)
			{
				$arList[$i]['URL'] = $arSBlock[$arList[$i]['SECTION_BLOCK']].$arList[$i]['SECTION_BLOCK'].'/'.$arList[$i]['ID'].'/';
				$arList[$i]['URL_BACK'] = $arSBlock[$arList[$i]['SECTION_BLOCK']];
			}
		}

        return $arList;
	}

	function Add($nSBlock, $arPar = array())
	{
	    global $LIB, $DB, $USER;

	    if(!$arSBlock = $LIB['SECTION_BLOCK']->ID($nSBlock)){
        	$this->Error = $LIB['SECTION_BLOCK']->Error;
        	return false;
        }
		if(!$arBlock = $LIB['BLOCK']->ID($arSBlock['BLOCK'])){
			$this->Error = $LIB['BLOCK']->Error;
			return false;
		}
		if($sError = $LIB['FIELD']->CheckAll('k2_block'.$arBlock['ID'], $arPar)){
       		$this->Error = $sError;
			return false;
        }

        if($nID = $DB->Insert("INSERT INTO `k2_block".$arSBlock['BLOCK']."` (`DATE_CREATED`, `USER_CREATED`, `ACTIVE`, `SORT`, `SECTION_BLOCK`, `CATEGORY`) VALUES (NOW(), '".$USER['ID']."', '".(int)$arPar['ACTIVE']."', '".(int)$arPar['SORT']."', '".$nSBlock."', '".(int)$arPar['CATEGORY']."')")){
        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_block'.$arBlock['ID'], 'PATH' => 'section/'.$arSBlock['SECTION'].'/'.$nSBlock), $arPar);
        	return $nID;
        }

		return false;
	}

	function Edit($nID, $nSBlock, $arPar = array(), $bFull = false)
	{
	    global $LIB, $DB, $USER;

	    if(!$arElement = $this->ID($nID, $nSBlock)){
        	return false;
        }
        if(!$arSBlock = $LIB['SECTION_BLOCK']->ID($nSBlock)){
			$this->Error = $LIB['SECTION_BLOCK']->Error;
			return false;
		}
		if(!$arBlock = $LIB['BLOCK']->ID($arSBlock['BLOCK'])){
			$this->Error = $LIB['BLOCK']->Error;
			return false;
		}
		if(!$bFull){
        	$arPar += $arElement;
        }
        if($sError = $LIB['FIELD']->CheckAll('k2_block'.$arBlock['ID'], $arPar)){
       		$this->Error = $sError;
			return false;
        }

        if($sError = $LIB['EVENT']->Execute('BEFORE_EDIT_BLOCK_ELEMENT', $arPar)){
        	$this->Error = $sError;
			return false;
        }

        if($DB->Query("UPDATE
        	`k2_block".$arSBlock['BLOCK']."`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`SORT` = '".(int)$arPar['SORT']."',
			`DATE_CHANGE` = NOW(),
			`USER_CHANGE` = '".$USER['ID']."',
			`CATEGORY` = '".(int)$arPar['CATEGORY']."'
        WHERE
        	ID = '".$nID."';
        ")){        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_block'.$arBlock['ID'], 'PATH' => 'section/'.$arSBlock['SECTION'].'/'.$nSBlock), $arPar);
            return true;
        }

        return false;
	}

	function Delete($nID, $nSBlock)
	{
    	global $DB, $LIB;

        if(!$arElm = $this->ID($nID, $nSBlock)){
        	return false;
        }

        if(!$arSBlock = $LIB['SECTION_BLOCK']->ID($nSBlock)){
        	$this->Error = $LIB['SECTION_BLOCK']->Error;
        	return false;
        }

        $LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_block'.$arSBlock['BLOCK'], 'ELEMENT' => $nID));

    	return $DB->Query("DELETE FROM `k2_block".$arSBlock['BLOCK']."` WHERE `ID` = '".(int)$nID."'");
	}
}
?>