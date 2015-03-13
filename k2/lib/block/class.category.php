<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class BlockCategory
{
	function ID($nID, $nBlock)
	{
		global $DB, $LIB;
		if($arCategory = $DB->Row("SELECT * FROM `k2_block".(int)$nBlock."category` WHERE ID = '".(int)$nID."'")){			return $arCategory;
		}
        $this->Error = 'Категория не найдена';
        return false;
	}

	function Rows($nBlock, $arFilter = array(), $arOrderBy = array(), $arSelect = array('*'), $arNav = array(), $arLimit = array())
	{
        global $LIB, $DB, $USER;

		if(!$arSelect){
			$arSelect = array('*');
		}elseif($arSelect[0] != '*'){
			$arSelect[] = 'ID';
			$arSelect[] = 'SECTION_BLOCK';
		}

		if(!$arNav['WEIGHT']){
        	$arNav['WEIGHT'] = 9;
        }
        $LIB['NAV']->Setting = array('WEIGHT' => (int)$arNav['WEIGHT'], 'SIZE' => (int)$arNav['SIZE'], 'TOTAL' => 0);

		$arFilter = array(
        'FROM' => 'k2_block'.(int)$nBlock.'category',
        'WHERE' => $arFilter,
        'ORDER_BY' => $arOrderBy,
        'SELECT' => $arSelect,
        'NAV' => $arNav);

        if($arLimit){
        	$arFilter['LIMIT'] = $arLimit;
 			$arFilter['NAV'] = false;
 			$LIB['NAV']->Setting = array();
        }

        $sSQL = $DB->CSQL($arFilter);

        if((!$arList = $DB->Rows($sSQL)) && $_GET['page']>1){
        	$_GET['page'] = 1;
        	$sSQL = $DB->CSQL($arFilter);
        	$arList = $DB->Rows($sSQL);
        }

		if($arList){
			$arCount = $DB->Row("SELECT FOUND_ROWS()");
			$LIB['NAV']->Setting['TOTAL'] = $arCount['FOUND_ROWS()'];
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
		if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }
		if($this->Error = $LIB['FIELD']->CheckAll('k2block'.$arBlock['ID'].'category', $arPar)){
			return false;
        }

        if($nID = $DB->Insert("INSERT INTO `k2_block".$arBlock['ID']."category` (`DATE_CREATED`, `USER_CREATED`, `SECTION_BLOCK`, `PARENT`, `ACTIVE`, `SORT`, `NAME`) VALUES (NOW(), '".$USER['ID']."', '".$nSBlock."', '".(int)$arPar['PARENT']."', '".(int)$arPar['ACTIVE']."', '".(int)$arPar['SORT']."', '".DBS($arPar['NAME'])."')")){
        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_block'.$arBlock['ID'].'category', 'PATH' => 'section/'.$arSBlock['SECTION'].'/'.$nSBlock), $arPar);
        	return $nID;
        }

		return false;
	}

	function Edit($nID, $nSBlock, $arPar = array(), $bFull = false)
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
	    if(!$arCategory = $this->ID($nID, $arBlock['ID'])){
        	return false;
        }

		if(!$bFull){
        	$arPar += $arCategory;
        }

        if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }

        if($sError = $LIB['FIELD']->CheckAll('k2block'.$arBlock['ID'].'category', $arPar)){
       		$this->Error = $sError;
			return false;
        }

        if($DB->Query("UPDATE
        	`k2_block".$arBlock['ID']."category`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`SORT` = '".(int)$arPar['SORT']."',
			`DATE_CHANGE` = NOW(),
			`USER_CHANGE` = '".$USER['ID']."',
			`PARENT` = '".(int)$arPar['PARENT']."',
			`NAME` = '".DBS($arPar['NAME'])."'
        WHERE
        	ID = '".$nID."';
        ")){        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_block'.$arBlock['ID'].'category', 'PATH' => 'section/'.$arSBlock['SECTION'].'/'.$nSBlock), $arPar);
            return true;
        }

        return false;
	}

	function Delete($nID, $nSBlock)
	{
    	global $DB, $LIB;

        if(!$arSBlock = $LIB['SECTION_BLOCK']->ID($nSBlock)){
        	$this->Error = $LIB['SECTION_BLOCK']->Error;
        	return false;
        }

        if(!$arCategory = $this->ID($nID, $arSBlock['BLOCK'])){
        	return false;
        }

        $arBlock = $LIB['BLOCK']->ID($arSBlock['BLOCK'], 1);

        $arCCategory = $this->Child(array('BLOCK' => $arSBlock['BLOCK'], 'ID' => $nID, 'RECURSIVE' => true));
        $arCCategory[] = $arCategory;

        for($i=0; $i<count($arCCategory); $i++)
		{
			$arElement = $LIB['BLOCK_ELEMENT']->Rows($arSBlock['BLOCK'], array('SECTION_BLOCK' => $nSBlock, 'CATEGORY' => $arCCategory[$i]['ID']), false, array('ID'));
			for($n=0; $n<count($arElement); $n++)
			{				$LIB['BLOCK_ELEMENT']->Delete($arElement[$n]['ID'], $nSBlock);
			}
			$LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_block'.$arSBlock['BLOCK'].'category', 'ELEMENT' => $arCCategory[$i]['ID']));
			$DB->Query("DELETE FROM `k2_block".$arSBlock['BLOCK']."category` WHERE `ID` = '".$arCCategory[$i]['ID']."'");
		}
    	return true;
	}

	function Back($arPar, $arList = array())
	{
		global $DB, $LIB;
	    if($arCategory = $DB->Rows("SELECT * FROM `k2_block".(int)$arPar['BLOCK']."category` WHERE `ID` = '".(int)$arPar['ID']."'".($arPar['ACTIVE']?" AND `ACTIVE` = 1":""))){
	    	$arList[] = $arCategory[0];
	    	if($arCategory[0]['PARENT']){
	    		$arPar['ID'] = $arCategory[0]['PARENT'];
	    		$arList = $this->Back($arPar, $arList);
	    	}else{
	    		$arList = array_reverse($arList);
	    	}
	    }
	    return $arList;
	}

	function Child($arPar, $arList = array())
	{
		global $DB, $LIB;
	    if($arCategory = $DB->Rows("SELECT * FROM `k2_block".(int)$arPar['BLOCK']."category` WHERE `PARENT` = '".(int)$arPar['ID']."'".($arPar['ACTIVE']?" AND `ACTIVE` = 1":"")." ORDER BY `SORT` ASC")){
	    	for($i=0; $i<count($arCategory); $i++)
	    	{	    		$arList[] = $arCategory[$i];
	    		if($arPar['RECURSIVE']){		    		$arPar['ID'] = $arCategory[$i]['ID'];
		    		$arList = $this->Child($arPar, $arList);
	    		}
	    	}
	    }
	    return $arList;
	}
}
?>