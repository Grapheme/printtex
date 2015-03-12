<?
$LIB['TOOL']->Delayed('TITLE', 'Восстановление пароля');
$LIB['NAV']->BackAdd(2, array('Восстановление пароля'));
session_start();
if($_GET['restore']){
	if($arUser = $DB->Row("SELECT `ID`, `LOGIN`, `EMAIL` FROM `k2_user` WHERE `RESTORE` = '".DBS($_GET['restore'])."'")){
    	$sPass = genPassword(8);
    	$DB->Query("UPDATE `k2_user` SET `PASSWORD` = '".md5(md5(PASSWORD_SALT).$sPass)."', `RESTORE` = '' WHERE `ID` = '".$arUser['ID']."';");
		$LIB['TOOL']->Delayed('TITLE', 'Пароль изменен');
		?><p>Логин: <span style="color:green"><?=$arUser['LOGIN']?></span></p>
		<p>Пароль: <span style="color:green"><?=$sPass?></span></p><?
	}else{
		Redirect($CURRENT['PATH'].'?action=password-recovery');
	}
}else{
	if($_POST){
	    if(!$_POST['EMAIL']){
	    	$sError = 'Введите эл. почту';
	    }elseif(isset($_SESSION['K2CAPTCHA']) && $_SESSION['K2CAPTCHA'] == $_POST['K2CAPTCHA']){
	   		if($arUser = $DB->Row("SELECT `ID`, `LOGIN`, `EMAIL` FROM `k2_user` WHERE `EMAIL` = '".DBS($_POST['EMAIL'])."'")){
	        	$sPass = genPassword(20);
	            $DB->Query("UPDATE `k2_user` SET `RESTORE` = '".$sPass."' WHERE `ID` = '".$arUser['ID']."';");

	        	$sBody = "Для смены пароля перейдите по следующей ссылке:\n";
	        	$sBody .= "http://".$_SERVER['SERVER_NAME'].$CURRENT['PATH']."?action=password-recovery&restore=".$sPass."\n";
	        	$sBody .= "\n-------\nДата отправки: ".date("Y-m-d H:i:s");
				sendMail($arUser['EMAIL'], $CURRENT['SITE']['EMAIL'], $sBody);
				Redirect($CURRENT['PATH'].'?action=password-recovery&complite=1');
	   		}
		}else{
			$sError = 'Введены неправильные символы на картинке';
		}
	}
	if($_GET['complite']){
		?><div class="success">Инструкция по восстановлению пароля отправлена на E-mail</div><?
	}elseif($sError){
		?><div class="error"><?=$sError?></div><?
	}else{
		?><p>Инструкция по восстановлению пароля будет отправлена на эл. почту</p><?
	}
	?><form action="<?=$CURRENT['PATH']?>?action=password-recovery" method="post" id="form">
		<p class="field">Эл. почта<br><input type="text" name="EMAIL" value="<?=html($_POST['EMAIL'])?>"></p>
		<p class="field">Введите символы на картинки<span class="star">*</span><br><input type="text" name="K2CAPTCHA" value=""></p>
		<p><img src="/k2/captcha/?<?=session_id()?>"></p>
		<p class="form"><span class="star">*</span> &mdash; поля, обязательные для заполнения</p>
		<p><input type="submit" value="Отправить"></p>
	</form><?
}
?>