<?
class ComponentElement
{
	function ID($nID, $nComponent)
	{
		global $DB, $LIB;
		if($arElement = $DB->Row("SELECT * FROM `k2_component".(int)$nComponent."` WHERE `ID` = '".(int)$nID."'")){
			return $arElement;
		}
        $this->Error = 'Элемент не найдена';
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
        'FROM' => 'k2_component'.$nComponent,
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

		if($this->Error = $LIB['FIELD']->CheckAll('k2_component'.$arComponent['ID'], $arPar)){
			return false;
        }

        if($nID = $DB->Insert("INSERT INTO `k2_component".$nComponent."` (`DATE_CREATED`, `USER_CREATED`, `COLLECTION`, `CATEGORY`, `ACTIVE`, `SORT`) VALUES (NOW(), '".$USER['ID']."', '".$nCollection."', '".(int)$arPar['CATEGORY']."', '".(int)$arPar['ACTIVE']."', '".(int)$arPar['SORT']."')")){
        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_component'.$nComponent, 'PATH' => 'component/'.$nComponent), $arPar);
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
        if($sError = $LIB['FIELD']->CheckAll('k2_component'.$arComponent['ID'], $arPar)){
       		$this->Error = $sError;
			return false;
        }

        if($DB->Query("UPDATE
        	`k2_component".$nComponent."`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`SORT` = '".(int)$arPar['SORT']."',
			`DATE_CHANGE` = NOW(),
			`USER_CHANGE` = '".$USER['ID']."',
			`COLLECTION` = '".(int)$arPar['COLLECTION']."'
        WHERE
        	ID = '".$nID."';
        ")){
        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_component'.$nComponent, 'PATH' => 'component/'.$nComponent), $arPar);
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
		for($i=0; $i<count($arComponent['ELEMENT_FIELD']); $i++)
		{
			if($arComponent['ELEMENT_FIELD'][$i]['TYPE'] == 4){
				if($arElement = $DB->Row("SELECT ".$arComponent['ELEMENT_FIELD'][$i]['FIELD']." AS `FILE` FROM `k2_component".$nComponent."` WHERE ID = '".$nID."'")){					$LIB['FILE']->Delete($arElement['FILE']);
				}
			}
		}
    	$DB->Query("DELETE FROM `k2_component".$nComponent."` WHERE `ID` = '".(int)$nID."'");
    	return true;
	}
}
?>