<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class User
{
	function ID($nID, $bFull = 0)
	{
		global $LIB, $DB;
		if($arUser = $DB->Row("SELECT * FROM `k2_user` WHERE `ID` = '".$nID."'")){
        	if($bFull){
            	$arUser['SETTING'] = $this->Setting($nID);
        	}
			return $arUser;
        }
        $this->Error = 'Пользователь не найден';
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
        'FROM' => 'k2_user',
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
		global $LIB, $DB, $USER, $SETTING;
        $arPar['LOGIN'] = trim($arPar['LOGIN']);

        if($sError = formCheck(array(
	    'LOGIN' => 'Логин',
	    'EMAIL' => 'E-mail',
	    'PASSWORD' => 'Пароль',
	    'PASSWORD_RETRY' => 'Повторите ввод пароля'), $arPar)){
       		$this->Error = $sError;
			return false;
        }

		if(!preg_match("#^[a-zа-я0-9 _\-\.@]+$#i", $arPar['LOGIN'])){
			$this->Error = 'Допустимые символы в логине a-zА-Я0-9_-.@';
			return false;
		}
		if($DB->Rows("SELECT 1 FROM `k2_user` WHERE `LOGIN` LIKE '".DBS($arPar['LOGIN'])."'")){
        	$this->Error = 'Укажите другой логин';
        	return false;
		}
		if(!preg_match("#^([a-z0-9]+([_\-\.=+~][a-z0-9]){0,1})+@([a-z0-9]+([_\-\.][a-z0-9]){0,1})+\.[a-z]{2,4}$#i", $arPar['EMAIL'])){
			$this->Error = 'Укажите верный E-mail';
			return false;
		}
		if($SETTING['AUTH_UNUQ_EMAIL'] && $DB->Row("SELECT 1 FROM `k2_user` WHERE `EMAIL` LIKE '".DBS($arPar['EMAIL'])."'")){
        	$this->Error = 'Укажите другой E-mail';
        	return false;
		}
		if(strlen($arPar['PASSWORD']) < 4){
			$this->Error = 'Пароль должен быть не менее 4 символов';
			return false;
		}
		if($arPar['PASSWORD'] != $arPar['PASSWORD_RETRY']){
			$this->Error = 'Повторите ввод пароля';
			return false;
		}

		if($sError = $LIB['FIELD']->CheckAll('k2_user', $arPar)){
       		$this->Error = $sError;
			return false;
        }
        if(!$arPar['USER_GROUP']){
        	$arPar['USER_GROUP'] = 2;
        }

		if($nID = $DB->Insert("
		INSERT INTO `k2_user`(
			`DATE_CREATED`,
			`USER_CREATED`,
			`ACTIVE`,
			`LOGIN`,
			`PASSWORD`,
			`EMAIL`,
			`USER_GROUP`
		)VALUES(
			NOW(), '".$USER['ID']."', ".(int)$arPar['ACTIVE'].", '".DBS($arPar['LOGIN'])."', '".md5(md5(PASSWORD_SALT).$arPar['PASSWORD'])."', '".DBS($arPar['EMAIL'])."', '".(int)$arPar['USER_GROUP']."'
		);
		")){
			mkdir($_SERVER['DOCUMENT_ROOT'].'/files/user/'.$nID);
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_user', 'PATH' => 'user/'.$nID), $arPar);
			return $nID;
		}

    	return false;
	}

	function Edit($nID, $arPar = array(), $bFull = 0)
	{
		global $LIB, $DB, $USER, $SETTING;
        if(!$arUser = $this->ID($nID)){
        	return false;
        }

        if(($arUser['USER_GROUP'] == 1) && ($USER['USER_GROUP'] != 1)){
        	$this->Error = 'Редактирование доступно только для группы администраторов';
        	return false;
        }

        if(!$bFull){
        	$arPar += $arUser;
        }

        if($sError = formCheck(array(
	    'LOGIN' => 'Логин',
	    'EMAIL' => 'E-mail'), $arPar)){
       		$this->Error = $sError;
			return false;
        }

        if(!preg_match("#^([a-z0-9]+([_\-\.=+~][a-z0-9]){0,1})+@([a-z0-9]+([_\-\.][a-z0-9]){0,1})+\.[a-z]{2,4}$#i", $arPar['EMAIL'])){
			$this->Error = 'Укажите верный E-mail';
			return false;
		}

        if($DB->Rows("SELECT 1 FROM `k2_user` WHERE `LOGIN` LIKE '".DBS($arPar['LOGIN'])."' AND ID != '".(int)$nID."'")){
        	$this->Error = 'Такой логин уже занят';
        	return false;
		}

		if($SETTING['AUTH_UNUQ_EMAIL'] && $DB->Rows("SELECT 1 FROM `k2_user` WHERE `EMAIL` LIKE '".DBS($arPar['EMAIL'])."' AND ID != '".(int)$nID."'")){
        	$this->Error = 'Такой E-mail уже занят';
        	return false;
		}

		if(strlen($arPar['PASSWORD'])<1){
			$arPar['PASSWORD'] = $arUser['PASSWORD'];
		}else{
			if($arPar['PASSWORD'] != $arPar['PASSWORD_RETRY']){
				$this->Error = 'Повторите ввод пароля';
				return false;
			}
			$arPar['PASSWORD'] = md5(md5(PASSWORD_SALT).$arPar['PASSWORD']);
		}

		if($USER['USER_GROUP'] != 1 || ($USER['ID'] == $arUser['ID'])){
			$arPar['USER_GROUP'] = $arUser['USER_GROUP'];
		}

        if($sError = $LIB['FIELD']->CheckAll('k2_user', $arPar)){
       		$this->Error = $sError;
			return false;
        }
        if($DB->Query("UPDATE `k2_user`
        SET
			`DATE_CHANGE` = NOW(),
			`USER_CHANGE` = '".$USER['ID']."',
			`ACTIVE` = ".(int)$arPar['ACTIVE'].",
			`LOGIN` = '".DBS($arPar['LOGIN'])."',
			`PASSWORD` = '".$arPar['PASSWORD']."',
			`EMAIL` = '".$arPar['EMAIL']."',
			`USER_GROUP` = '".(int)$arPar['USER_GROUP']."'
        WHERE
        	`ID` = '".$nID."';
        ")){
            $LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_user', 'PATH' => 'user/'.$nID), $arPar);
	        return $nID;
        }

    	return false;
	}

	function Delete($nID)
	{
    	global $LIB, $DB, $USER;

    	if(!$arUser = $this->ID($nID)){
        	return false;
        }
        if($USER['ID'] == $arUser['ID']){
        	return false;
        }
        if(($arUser['USER_GROUP'] == 1) && ($USER['USER_GROUP'] != 1)){
        	return false;
        }

		$LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_user', 'ELEMENT' => $nID));

        if($DB->Query("DELETE FROM `k2_user` WHERE ID = '".$nID."'")){
        	return true;
		}

		return false;
	}

    function Auth($bEmail = false)
    {
		global $DB, $LIB, $USER, $SETTING;
		if($USER){
			return $USER;
		}
		if(($_POST['AUTH_LOGIN'] || ($bEmail && $_POST['AUTH_EMAIL'])) && $_POST['AUTH_PASSWORD']){
		    if($arUser = $DB->Row("SELECT * FROM `k2_user` WHERE `".($bEmail?'EMAIL':'LOGIN')."` = '".DBS($_POST[$bEmail?'AUTH_EMAIL':'AUTH_LOGIN'])."' AND `PASSWORD` = '".md5(md5(PASSWORD_SALT).$_POST['AUTH_PASSWORD'])."' AND `ACTIVE` = 1")){
                $nRememberTime = ($_POST['AUTH_REMEMBER']?time()+$SETTING['AUTH_TIME']*60:time()+3600);
                $sHash = md5($arUser['LOGIN'].$_POST['AUTH_PASSWORD']);
                $DB->Query("UPDATE `k2_user` SET `HASH` = '".$sHash."' WHERE `ID` = '".$arUser['ID']."'");
                setcookie('K2_AUTH', $sHash.':'.(int)$_POST['AUTH_REMEMBER'], $nRememberTime, '/', '');
                unset($arUser['PASSWORD']);

                if(defined('ADMIN_MODE')){
            		$arUser['SETTING'] = $this->Setting($arUser['ID']);
	        	}

	        	$arGroup = $LIB['USER_GROUP']->ID($arUser['USER_GROUP']);
                $arUser['PERMISSION']['DEFAULT'] = $arGroup['PERMISSION_DEFAULT'];
                $arUser['PERMISSION']['SITE'] = $arGroup['PERMISSION_SITE'];
                $arUser['PERMISSION']['SECTION'] = $arGroup['PERMISSION_SECTION'];

                return $arUser;
			}
		}elseif($_COOKIE['K2_AUTH'] && $_COOKIE['K2_AUTH'] != -1){
			if($_SERVER['HTTP_REFERER'] && (strpos($_SERVER['HTTP_REFERER'], 'http://'.$_SERVER['SERVER_NAME']) !== 0)){
				return false;
			}
			$arExp = explode(':', $_COOKIE['K2_AUTH']);
			$sHash = $arExp[0];
			$bRemember = $arExp[1];

			if($arUser = $DB->Row("SELECT * FROM `k2_user` WHERE `HASH` = '".DBS($sHash)."' AND `ACTIVE` = 1")){
				$nRememberTime = $bRemember?time()+$SETTING['AUTH_TIME']*60:time()+3600;
				setcookie('K2_AUTH', $arUser['HASH'].':'.(int)$bRemember, $nRememberTime, '/', '');
				unset($arUser['PASSWORD']);

				if(defined('ADMIN_MODE')){
            		$arUser['SETTING'] = $this->Setting($arUser['ID']);
	        	}

	        	$arGroup = $LIB['USER_GROUP']->ID($arUser['USER_GROUP']);
                $arUser['PERMISSION']['DEFAULT'] = $arGroup['PERMISSION_DEFAULT'];
                $arUser['PERMISSION']['SITE'] = $arGroup['PERMISSION_SITE'];
                $arUser['PERMISSION']['SECTION'] = $arGroup['PERMISSION_SECTION'];

				return $arUser;
			}
		}
    	return false;
    }

    function Setting($nUser)
	{
    	global $DB;
    	$arList = array();
    	$arSetting = $DB->Rows("SELECT * FROM `k2_user_setting` WHERE `USER` = '".(int)$nUser."'");
       	for($i=0; $i<count($arSetting); $i++)
       	{
       		$arList[$arSetting[$i]['ACTION']] = unserialize($arSetting[$i]['DATA']);
       		if(!$arList[$arSetting[$i]['ACTION']]){
       			$arList[$arSetting[$i]['ACTION']] = $arSetting[$i]['DATA'];
       		}
       	}
		return $arList;
	}

    function Logout()
    {
    	if($_COOKIE['K2_AUTH'] != -1){
    		setcookie('K2_AUTH', -1, time()+30758400, '/', '', false, true);
	    	if(defined('ADMIN_MODE')){	    		Redirect('/k2/admin/');
	    	}
	    	Redirect();
    	}
    }

    function IP()
	{
		$arKey = array('REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_VIA', 'HTTP_X_COMING_FROM', 'HTTP_COMING_FROM', 'HTTP_CLIENT_IP');
		foreach ($arKey as $sValue)
		{
			if(!empty($_SERVER[$sValue])){
		    	if($sIP = preg_match("#^([0-9]{1,3}\.){3}[0-9]{1,3}#", $_SERVER[$sValue], $arMath)){
		        	return $arMath[0];
		        }
		    }
		}
		return false;
	}

	function Login($nID)
	{
		global $DB;
		if($arUser = $DB->Rows("SELECT `LOGIN` FROM `k2_user` WHERE `ID` = '".(int)$nID."'")){
			return $arUser[0]['LOGIN'];
		}
	    return false;
	}
}
?>