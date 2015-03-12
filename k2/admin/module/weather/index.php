<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'WEATHER');
tab(array(array('Модули', '/module/'), array('Прогноз погоды', '/module/currency/', 1)));
tab_(array(array('Настройки', '/module/weather/', 1), array('Шаблоны', '/module/weather/template/')));

$arModule = $LIB['MODULE']->ID('WEATHER');
if($_POST){
	$sError = formCheck(array('CODE' => 'Код города'), $_POST['SETTING']);
	if($sError){
		$LIB['MODULE']->Error = $sError;
	}else{
		if($arRow = $DB->Row("SELECT `NAME` FROM `k2_mod_weather_city` WHERE `CODE` = '".$_POST['SETTING']['CODE']."'")){
			$_POST['SETTING']['CITY_NAME'] = $arRow['NAME'];
		}
		if($LIB['MODULE']->Edit('WEATHER', $_POST)){
			Redirect('/k2/admin/module/weather/?complite=1');
		}else{
			echo $LIB['MODULE']->Error;
		}
	}
}else{
	$_POST = $arModule;
}

?><div class="content">
    <form action="/k2/admin/module/weather/" method="post" enctype="multipart/form-data" class="form">
    	<?formError($LIB['MODULE']->Error)?>
    	<input type="hidden" name="ACTIVE" value="0">
        <div class="item">
			<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
			if($_POST['ACTIVE']){
				?> checked="checked"<?
			}
			?>>Активность</label>
		</div>
        <div class="item">
			<div class="name">Город<span class="star">*</span></div>
			<div class="field"><select name="SETTING[CITY]" id="city"><?
		    $arCity = $DB->Rows("SELECT * FROM `k2_mod_weather_city` ORDER BY `NAME` ASC");
			for($i=0; $i<count($arCity); $i++)
			{
				?><option value="<?=$arCity[$i]['CODE']?>"<?
				if($_POST['SETTING']['CITY'] == $arCity[$i]['CODE']){
					?> selected="selected"<?
				}
				?>><?=$arCity[$i]['NAME']?></option><?
			}
		    ?></select></div>
		</div>
		<div class="item">
			<div class="name">Код города</div>
			<div class="field"><input type="text" name="SETTING[CODE]" value="<?=(int)$_POST['SETTING']['CODE']?>" id="code"></div>
		</div>
        <div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>