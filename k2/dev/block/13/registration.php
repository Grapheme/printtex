<?
$LIB['TOOL']->Delayed('TITLE', 'Регистрация');
$LIB['NAV']->BackAdd(2, array('Регистрация'));
session_start();
$sError = '';
if($_POST){
	if(isset($_SESSION['K2CAPTCHA']) && $_SESSION['K2CAPTCHA'] == $_POST['K2CAPTCHA']){
		$_POST['ACTIVE'] = 1;
		if($nID = $LIB['USER']->Add($_POST)){
	        $_POST['AUTH_LOGIN'] = strip_tags($_POST['LOGIN']);
	        $_POST['AUTH_PASSWORD'] = $_POST['PASSWORD'];
	        $_POST['AUTH_REMEMBER'] = 1;
			$USER = $LIB['USER']->Auth();
			Redirect($CURRENT['PATH']);
		}else{
			$sError = $LIB['USER']->Error;
		}
	}else{
		$sError = 'Введены неправильные символы на картинки';
	}
}
?><form action="<?=$CURRENT['PATH']?>?action=registration" method="post" enctype="multipart/form-data" id="form">
	<table class="table">
		<tr>
			<td width="100" class="r"></td>
			<td class="field"><?
			if($sError){
				?><div class="error"><?=$sError?></div><?
			}
			?></td>
		</tr>
		<tr>
			<td width="100" class="r">Логин<span class="star">*</span></td>
			<td class="field"><input type="text" name="LOGIN" value="<?=html($_POST['LOGIN'])?>"></td>
		</tr>
		<tr>
			<td class="r">Пароль<span class="star">*</span></td>
			<td class="field"><input type="password" name="PASSWORD" value="<?=html($_POST['PASSWORD'])?>"></td>
		</tr>
		<tr>
			<td class="r">Повтор пароля<span class="star">*</span></td>
			<td class="field"><input type="password" name="PASSWORD_RETRY" value="<?=html($_POST['PASSWORD_RETRY'])?>"></td>
		</tr>
		<tr>
			<td class="r">Эл. почта<span class="star">*</span></td>
			<td class="field"><input type="text" name="EMAIL" value="<?=html($_POST['EMAIL'])?>"></td>
		</tr><?
		$arField = $LIB['FIELD']->Rows(3);
		for($i=0; $i<count($arField); $i++)
		{
			echo $LIB['FORM']->Element($arField[$i]['ID'], '<tr class="field"><td class="r">%NAME%</td><td>%FIELD%</td></tr>');
		}
		?><tr class="chet">
			<td class="r">Зашитный код<span class="star">*</span></td>
			<td class="field"><input type="text" name="K2CAPTCHA" value=""></td>
		</tr>
		<tr>
			<td></td>
			<td><img src="/k2/captcha/?<?=session_id()?>"></td>
		</tr>
		<tr>
			<td></td>
			<td class="note"><span class="star">*</span> &mdash; поля, обязательные для заполнения</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Отправить"></td>
		</tr>
	</table>
</form><?
unset($_SESSION['K2CAPTCHA']);
?>