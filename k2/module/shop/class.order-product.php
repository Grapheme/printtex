<?
class ShopOrderProduct
{
	function ID($nID)
	{
		global $LIB, $DB;
		if($arPayment = $DB->Row("SELECT * FROM `k2_mod_shop_order_product` WHERE `ID` = '".$nID."'")){
			return $arPayment;
        }
        $this->Error = 'Продукт не найден';
		return false;
	}

	function Rows($arFilter = array(), $arOrderBy = array(), $arSelect = array('*'), $arNav = array(), $arLimit = array())
	{
        global $LIB, $DB, $USER;

		if(!$arSelect){
			$arSelect = array('*');
		}

		if(!$arNav['WEIGHT']){
        	$arNav['WEIGHT'] = 9;
        }
        $LIB['NAV']->Setting = array('WEIGHT' => (int)$arNav['WEIGHT'], 'SIZE' => (int)$arNav['SIZE'], 'TOTAL' => 0);

		$arFilter = array(
        'FROM' => 'k2_mod_shop_order_product',
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

	function Add($arPar = array())
	{
		global $LIB, $DB, $USER;

		if($sError = formCheck(array(
			'SHOP_ORDER' => 'Номер заказа',
			'NAME' => 'Название',
			'PRICE' => 'Цена',
			'QUANTITY' => 'Количество'
			), $arPar)){
       		$this->Error = $sError;
			return false;
        }

        if($nID = $DB->Insert("
			INSERT INTO `k2_mod_shop_order_product`(
				`SHOP_ORDER`,
				`NAME`,
				`PRICE`,
				`QUANTITY`,
				`DATA_ORDER`
			)VALUES(
				'".(int)$arPar['SHOP_ORDER']."', '".DBS($arPar['NAME'])."', '".DBS($arPar['PRICE'])."', '".(int)$arPar['QUANTITY']."', '".DBS(serialize($arPar['DATA_ORDER']))."'
			);")
        ){
        	return $nID;
		}
    	return false;
	}

	function Delete($nID)
	{
    	global $LIB, $DB;

		$DB->Query("DELETE FROM `k2_mod_shop_order_product` WHERE ID = '".$nID."'");

		return true;
	}
}
?>