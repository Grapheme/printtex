<?
$LIB['TOOL']->Delayed('TITLE', 'Авторизация');
$LIB['NAV']->BackAdd(2, array('Авторизация'));
if($_POST){
	$_POST['AUTH_LOGIN'] = $_POST['LOGIN'];
 	$_POST['AUTH_PASSWORD'] = $_POST['PASSWORD'];
	$_POST['AUTH_REMEMBER'] = $_POST['REMEMBER'];
	if($USER = $LIB['USER']->Auth()){
		Redirect($CURRENT['PATH']);
	}else{
		?><div class="error">Неправильный логин/пароль</div><?
	}
}
?><form action="<?=$CURRENT['PATH']?>?action=authorization" method="post" id="form">
	<p class="field">Логин<span class="star">*</span><br><input type="text" name="LOGIN" value="<?=html($_POST['LOGIN'])?>"></p>
	<p class="field">Пароль<span class="star">*</span><br><input type="password" name="PASSWORD" value="<?=html($_POST['PASSWORD'])?>"></p>
	<p><label><input type="checkbox" name="REMEMBER" value="1"<?
	if($_POST['REMEMBER']){
		?> checked="checked"<?
	}
	?>>запомнить меня</label></p>
	<p class="note"><span class="star">*</span> &mdash; поля, обязательные для заполнения</p>
	<p><input type="submit" value="Отправить"></p>
</form>
<p>Если вы забыли пароль воспользуйтесь <a href="<?=$CURRENT['PATH']?>?action=password-recovery">формой восстановления</a></p>