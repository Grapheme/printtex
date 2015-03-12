<?
class Email
{
	function ID($nID)
	{
		global $LIB, $DB;
		if($arPayer = $DB->Row("SELECT * FROM `k2_mod_email` WHERE `ID` = '".$nID."'")){
			return $arPayer;
        }
        $this->Error = 'Почтовый шаблон не найден';
		return false;
	}

	function Rows()
	{
    	global $DB;
    	$arPayer = $DB->Rows("SELECT * FROM `k2_mod_email` ORDER BY `ID` ASC");
		return $arPayer;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;

		if($sError = formCheck(array('NAME' => 'Название', 'TYPE' => 'Тип', 'SUBJECT' => 'Тема письма', 'FROM' => 'E-mail отправителя'))){
       		$this->Error = $sError;
			return false;
        }

        if($DB->Row("SELECT 1 FROM `k2_mod_email` WHERE `TYPE` = '".DBS($_POST['TYPE'])."'")){        	$this->Error = 'Почтовый шаблон с таким типом уже существует';
			return false;
        }

        if($nID = $DB->Insert("
		INSERT INTO `k2_mod_email` (
			`TYPE`,
			`NAME`,
			`SUBJECT`,
			`FROM`,
			`TEMPLATE`
		)VALUES(
			'".DBS($arPar['TYPE'])."', '".DBS($arPar['NAME'])."', '".DBS($arPar['SUBJECT'])."', '".DBS($arPar['FROM'])."', '".DBS($arPar['TEMPLATE'])."'
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

        if($sError = formCheck(array('NAME' => 'Название', 'TYPE' => 'Тип', 'SUBJECT' => 'Тема письма', 'FROM' => 'E-mail отправителя'))){
       		$this->Error = $sError;
			return false;
        }

        if($DB->Row("SELECT 1 FROM `k2_mod_email` WHERE `TYPE` = '".DBS($_POST['TYPE'])."' AND `ID` != '".$arEmail['ID']."'")){
        	$this->Error = 'Почтовый шаблон с таким типом уже существует';
			return false;
        }

        if($DB->Query("UPDATE `k2_mod_email`
        SET
			`TYPE` = '".DBS($arPar['TYPE'])."',
			`NAME` = '".DBS($arPar['NAME'])."',
			`SUBJECT` = '".DBS($arPar['SUBJECT'])."',
			`FROM` = '".DBS($arPar['FROM'])."',
			`TEMPLATE` = '".DBS($arPar['TEMPLATE'])."'
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

		$DB->Query("DELETE FROM `k2_mod_email` WHERE ID = '".(int)$nID."'");

		return true;
	}

	function Send($sTo, $sType, $arField)
	{
		global $DB, $CURRENT;

		if(!CorrectEmail($sTo)){			$this->Error = 'Укажите верный E-mail';
			return false;
		}
		if(!$arTemplate = $DB->Row("SELECT * FROM `k2_mod_email` WHERE `TYPE` = '".DBS($sType)."'")){
			$this->Error = 'Почтовый шаблон не найден';
			return false;
        }
        $arField['SERVER_NAME'] = $_SERVER['SERVER_NAME'];
        $arField['SITE_NAME'] = $CURRENT['SITE']['NAME'];
        $arField['FROM_EMAIL'] = $CURRENT['SITE']['EMAIL'];

        $sSubject = $arTemplate['SUBJECT'];
        $sFrom = $arTemplate['FROM'];
        $sBody = $arTemplate['TEMPLATE'];
        foreach($arField as $sKey => $sValue)
        {        	$sBody = str_replace('%'.$sKey.'%', $sValue, $sBody);
        	$sSubject = str_replace('%'.$sKey.'%', $sValue, $sSubject);
        	$sFrom = str_replace('%'.$sKey.'%', $sValue, $sFrom);
        }
        $sBody = nl2br($sBody);
        $mail = new PHPMailer();
		$mail->AddReplyTo($sFrom);
		$mail->SetFrom($sFrom);
		$mail->AddAddress($sTo);
		$mail->Subject = $sSubject;
		$mail->MsgHTML($sBody);
		$mail->XMailer = 'K2';
		$mail->CharSet = 'utf-8';
		$mail->IsHTML(true);

		if(!$mail->Send()){
			$this->Error = $mail->ErrorInfo;
			return false;
		}

		return true;
	}
}
?>