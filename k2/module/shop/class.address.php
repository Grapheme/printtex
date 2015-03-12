<?
class ShopAddress
{
	function ID($nUser)
	{
		global $DB;

		if($arElement = $DB->Row("SELECT * FROM `k2_mod_shop_address` WHERE `ID` = '".(int)$nUser."'")){
			return $arElement;
		}
        $this->Error = 'Адрес не найден';
        return false;
	}

	function Update($arPar = array())
	{
	    global $LIB, $DB, $USER;

		if($this->Error = $LIB['FIELD']->CheckAll('k2_mod_shop_address', $arPar)){
			return false;
        }

        if(!$this->ID($USER['ID'])){
        	if(!$DB->Insert("INSERT INTO `k2_mod_shop_address` (`ID`) VALUES ('".$USER['ID']."')")){        		return false;
        	}
        }
        $LIB['FIELD']->Update(array('ID' => $USER['ID'], 'TABLE' => 'k2_mod_shop_address'), $arPar);
        return true;
	}

	function Delete($nUser)
	{
    	global $DB, $MOD;

    	$DB->Query("DELETE FROM `k2_mod_shop_address` WHERE `USER` = '".(int)$nUser."'");
    	return true;
	}
}
?>