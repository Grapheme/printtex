<?
class ShopPayerElement
{
	function ID($nID, $nPayer)
	{
		global $DB, $MOD;
		if(!$arPayer = $MOD['SHOP_PAYER']->ID($nPayer)){
			$this->Error = $MOD['SHOP_PAYER']->Error;
			return false;
		}
		if($arElement = $DB->Row("SELECT * FROM `k2_mod_shop_payer".$arPayer['ID']."` WHERE `ID` = '".(int)$nID."'")){
			return $arElement;
		}
        $this->Error = 'Элемент не найден';
        return false;
	}

	function Rows($nPayer, $arFilter = array(), $arOrderBy = array(), $arSelect = array('*'), $arNav = array(), $arLimit = array())
	{        global $LIB, $DB, $USER;

		$nPayer = (int)$nPayer;

		if(!$arSelect){			$arSelect = array('*');
		}

		if(!$arNav['WEIGHT']){
        	$arNav['WEIGHT'] = 9;
        }
        $LIB['NAV']->Setting = array('WEIGHT' => (int)$arNav['WEIGHT'], 'SIZE' => (int)$arNav['SIZE'], 'TOTAL' => 0);

		$arFilter = array(
        'FROM' => 'k2_mod_shop_payer'.$nPayer,
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
		}

        return $arList;
	}

	function Add($nPayer, $arPar = array())
	{
	    global $MOD, $DB, $USER, $LIB;

	    if(!$arPayer = $MOD['SHOP_PAYER']->ID($nPayer)){
        	$this->Error = $MOD['SHOP_PAYER']->Error;
        	return false;
        }

        if($sError = formCheck(array(
			'SHOP_ORDER' => 'Номер заказа'
			), $arPar)){
       		$this->Error = $sError;
			return false;
        }

		if($sError = $LIB['FIELD']->CheckAll('k2_mod_shop_payer'.$nPayer, $arPar)){
       		$this->Error = $sError;
			return false;
        }
        if($nID = $DB->Insert("INSERT INTO `k2_mod_shop_payer".$nPayer."` (`USER`, `SHOP_ORDER`) VALUES ('".$USER['ID']."', '".(int)$arPar['SHOP_ORDER']."')")){
        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_mod_shop_payer'.$nPayer), $arPar);
        	return $nID;
        }

		return false;
	}

	function Delete($nID, $nPayer)
	{
    	global $DB, $MOD;

        if(!$arPayer = $MOD['SHOP_PAYER']->ID($nPayer)){
        	$this->Error = $MOD['SHOP_PAYER']->Error;
        	return false;
        }

    	$DB->Query("DELETE FROM `k2_mod_shop_payer".$nPayer."` WHERE `ID` = '".(int)$nID."'");
    	return true;
	}
}
?>