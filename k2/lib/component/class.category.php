<?
class ComponentCategory
{
	function ID($nID, $nComponent)
	{
		global $DB, $LIB;
		if($arCategory = $DB->Row("SELECT * FROM `k2_component".(int)$nComponent."category` WHERE `ID` = '".(int)$nID."'")){			return $arCategory;
		}
        $this->Error = 'Категория не найдена';
        return false;
	}

	function Rows($nComponent, $arFilter = array(), $arOrderBy = array(), $arSelect = array('*'), $arNav = array(), $arLimit = array())
	{
        global $LIB, $DB, $USER;

		$nComponent = (int)$nComponent;

		if(!$arSelect){
			$arSelect = array('*');
		}

		if(!$arNav['WEIGHT']){
        	$arNav['WEIGHT'] = 9;
        }
        $LIB['NAV']->Setting = array('WEIGHT' => (int)$arNav['WEIGHT'], 'SIZE' => (int)$arNav['SIZE'], 'TOTAL' => 0);

		$arFilter = array(
        'FROM' => 'k2_component'.$nComponent.'category',
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

	function Add($nComponent, $nCollection, $arPar = array())
	{
	    global $LIB, $DB, $USER;

		if(!$arComponent = $LIB['COMPONENT']->ID($nComponent)){
        	$this->Error = $LIB['COMPONENT']->Error;
        	return false;
        }

		if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }
		if($this->Error = $LIB['FIELD']->CheckAll('k2_component'.$arComponent['ID'].'category', $arPar)){
			return false;
        }

        if($nID = $DB->Insert("INSERT INTO `k2_component".$nComponent."category` (`DATE_CREATED`, `USER_CREATED`, `COLLECTION`, `PARENT`, `ACTIVE`, `SORT`, `NAME`) VALUES (NOW(), '".$USER['ID']."', '".$nCollection."', '".(int)$arPar['PARENT']."', '".(int)$arPar['ACTIVE']."', '".(int)$arPar['SORT']."', '".DBS($arPar['NAME'])."')")){
        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_component'.$nComponent.'category', 'PATH' => 'component/'.$nComponent), $arPar);
        	return $nID;
        }

		return false;
	}

	function Edit($nID, $nComponent, $nCollection, $arPar = array(), $bFull = 0)
	{
	    global $LIB, $DB, $USER;

	    if(!$arComponent = $LIB['COMPONENT']->ID($nComponent)){
			$this->Error = $LIB['COMPONENT']->Error;
			return false;
		}
	    if(!$arCategory = $this->ID($nID, $nComponent)){
        	return false;
        }

		if(!$bFull){
        	$arPar += $arCategory;
        }
        if($sError = $LIB['FIELD']->CheckAll('k2_component'.$arComponent['ID'].'category', $arPar)){
       		$this->Error = $sError;
			return false;
        }

        if($DB->Query("UPDATE
        	`k2_component".$nComponent."category`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`SORT` = '".(int)$arPar['SORT']."',
			`DATE_CHANGE` = NOW(),
			`USER_CHANGE` = '".$USER['ID']."',
			`PARENT` = '".(int)$arPar['PARENT']."',
			`NAME` = '".DBS($arPar['NAME'])."'
        WHERE
        	ID = '".$nID."';
        ")){        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_component'.$nComponent.'category', 'PATH' => 'component/'.$nComponent), $arPar);
            return true;
        }

        return false;
	}

	function Delete($nID, $nComponent)
	{
    	global $DB, $LIB;

        if(!$arComponent = $LIB['COMPONENT']->ID($nComponent, 1)){
        	$this->Error = $LIB['COMPONENT']->Error;
        	return false;
        }

        if(!$arCategory = $this->ID($nID, $nComponent)){
        	return false;
        }

        $arCCategory = $this->Child(array('COMPONENT' => $nComponent, 'ID' => $nID, 'RECURSIVE' => true));
        $arCCategory[] = $arCategory;

        for($i=0; $i<count($arCCategory); $i++)
		{
			$arElement = $LIB['COMPONENT_ELEMENT']->Rows($nComponent, array('CATEGORY' => $arCCategory[$i]['ID']), false, array('ID'));
			for($n=0; $n<count($arElement); $n++)
			{				$LIB['COMPONENT_ELEMENT']->Delete($arElement[$n]['ID'], $nComponent);
			}
			for($n=0; $n<count($arComponent['CATEGORY_FIELD']); $n++)
			{
				if($arComponent['CATEGORY_FIELD'][$n]['TYPE'] == 4){
					if($arCFile = $DB->Row("SELECT ".$arComponent['CATEGORY_FIELD'][$n]['FIELD']." AS `FILE` FROM `k2_component".$nComponent."category` WHERE ID = '".$arCCategory[$i]['ID']."'")){
						$LIB['FILE']->Delete($arCFile['FILE']);
					}
				}
			}
			$DB->Query("DELETE FROM `k2_component".$nComponent."category` WHERE `ID` = '".(int)$arCCategory[$i]['ID']."'");
		}
    	return true;
	}

	function Back($arPar, $arList = array())
	{
		global $DB, $LIB;
	    if($arCategory = $DB->Rows("SELECT * FROM `k2_component".(int)$arPar['COMPONENT']."category` WHERE `ID` = '".(int)$arPar['ID']."'".($arPar['ACTIVE']?" AND `ACTIVE` = 1":""))){
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

	    if($arCategory = $DB->Rows("SELECT * FROM `k2_component".(int)$arPar['COMPONENT']."category` WHERE `PARENT` = '".(int)$arPar['ID']."'".($arPar['ACTIVE']?" AND `ACTIVE` = 1":"")." ORDER BY `SORT` ASC")){
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