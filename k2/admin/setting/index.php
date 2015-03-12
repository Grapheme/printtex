<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Настройки', '/setting/', 1), array('Списки', '/setting/select/'), array('Сайты', '/setting/site/'), array('Обновления', '/setting/update/'), array('Инструменты', '/setting/tool/')));

if($_POST){
	foreach($_POST as $sKey=>$sValue)
	{
		if($sKey == 'AUTH_TIME' && $sValue <1){
			$sValue = $_POST['AUTH_TIME'] = 1;
		}
		$DB->Query("UPDATE `k2_setting` SET `SETTING` = '".DBS($sValue)."' WHERE TYPE = '".DBS($sKey)."'");
	}
}else{
	$_POST = $SETTING;
}
?>
<div class="content">
	<h1>Системные настройки</h1><?
	if($_GET['complite']){
		?><div class="complite">Данные сохранены</div><?
	}
	?>
    <form action="?complite=1" method="post" enctype="multipart/form-data" class="form">
    	<div class="item">
			<div class="name">Время действия авторизации(в минутах)<span class="star">*</span></div>
			<div class="field"><input type="text" name="AUTH_TIME" value="<?=htmlspecialchars($_POST['AUTH_TIME'])?>"></div>
		</div>
		<div class="item">
			<input type="hidden" name="AUTH_UNUQ_EMAIL" value="0"><label><input type="checkbox" name="AUTH_UNUQ_EMAIL" value="1"<?
			if($_POST['AUTH_UNUQ_EMAIL']){
				?> checked<?
			}
			?>>Регистрировать пользователей только с уникальными E-mail</label>
		</div>
		<div class="item">
			<input type="hidden" name="DEBUG_PANEL" value="0"><label><input type="checkbox" name="DEBUG_PANEL" value="1"<?
			if($_POST['DEBUG_PANEL']){
				?> checked<?
			}
			?>>Отображать на сайте панель отладки</label>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>