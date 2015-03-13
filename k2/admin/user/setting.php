<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('USER');

$arField['ID'] =           array('NAME' => 'ID', 'FORMAT' => '', 'ALIGN' => 'center', 'ACTIVE' => 1);
$arField['ACTIVE'] =       array('NAME' => 'Активность', 'FORMAT' => 'TORF', 'ALIGN' => 'center', 'ACTIVE' => 0);
$arField['DATE_CREATED'] = array('NAME' => 'Дата создания', 'FORMAT' => 'DATE', 'ALIGN' => 'center', 'ACTIVE' => 0);
$arField['DATE_CHANGE'] =  array('NAME' => 'Дата изменения', 'FORMAT' => 'DATE', 'ALIGN' => 'center', 'ACTIVE' => 0);
$arField['USER_CREATED'] = array('NAME' => 'Кем создана', 'FORMAT' => 'USER', 'ALIGN' => 'center', 'ACTIVE' => 0);
$arField['USER_CHANGE'] =  array('NAME' => 'Кем изменена', 'FORMAT' => 'USER', 'ALIGN' => 'center', 'ACTIVE' => 0);
$arField['LOGIN'] =        array('NAME' => 'Логин', 'FORMAT' => '', 'ALIGN' => 'left', 'ACTIVE' => 1);
$arField['EMAIL'] =        array('NAME' => 'E-mail', 'FORMAT' => '', 'ALIGN' => 'left', 'ACTIVE' => 1);
$arField['USER_GROUP'] =   array('NAME' => 'Группа', 'FORMAT' => 'USER_GROUP', 'ALIGN' => 'left', 'ACTIVE' => 1);

$arBField = $LIB['FIELD']->Rows('k2_user');
for($i=0; $i<count($arBField); $i++)
{
	$sFormat = '';
	$arSetting = unserialize($arBField[$i]['SETTING']);
	if($arBField[$i]['TYPE'] == 4){
    	$sFormat = 'FILE';
	}
	if($arBField[$i]['TYPE'] == 2){
 		$sFormat = 'TORF';
	}
	if($arBField[$i]['TYPE'] == 3){
 		$sFormat = 'SELECT';
	}
	if($arBField[$i]['TYPE'] == 5){
 		$sFormat = 'RELATION';
	}
	$arField[$arBField[$i]['FIELD']] = array('NAME' => $arBField[$i]['NAME'], 'FORMAT' => $sFormat, 'ALIGN' => 'left', 'ACTIVE' => 0);
}

if($arSSetting = userSettingView(false, array('TYPE' => 10))){
	$arRows = $DB->Rows("SHOW COLUMNS FROM `k2_user`");
 	for($i=0; $i<count($arRows); $i++)
	{
		$arIssetField[$arRows[$i]['Field']] = 1;
	}
	$arNewField = array();
	$arData = unserialize($arSSetting['DATA']);
	foreach($arData as $sKey => $arValue)
	{
		if($arIssetField[$sKey]){
			$arNewField[$sKey] = $arValue;
		}
   	}
   	$arField = $arNewField;
}

?><form action="setting-save.php" method="post">
	<table width="100%" class="table">
	   	<tr>
	   		<th class="first"><div class="icon moveWhite" title="Сортировка"></div></th>
	   		<th><input type="checkbox" title="Вкл/выкл показ полей" onclick="table.check.all(this, '.sf-body')"></th>
	   		<th width="99%">Поле</th>
	   		<th>Выравнивание</th>
	   	</tr>
	   	<tbody class="sf-body"><?
	   	foreach($arField as $sField=>$arProp)
		{
			?><tr class="<?
			if($i%2){
				?> odd<?
			}
			?>">
				<td class="sf-td"><div class="icon move"></div></td>
				<td><input type="checkbox" name="ACTIVE[<?=$sField?>]" value="1"<?
				if($arProp['ACTIVE']){					?> checked<?
				}
				?>></td>
				<td><input type="hidden" name="FIELD[<?=$sField?>]" value="<?=$sField?>">
				<input type="hidden" name="FORMAT[<?=$sField?>]" value="<?=$arProp['FORMAT']?>">
				<input type="hidden" name="ALIGN[<?=$sField?>]" id="align<?=$i?>" value="<?=$arProp['ALIGN']?>">
	   			<input type="hidden" name="NAME[<?=$sField?>]" value="<?=html($arProp['NAME'])?>">
				<?=html($arProp['NAME'])?></td>
				<td align="center"><a class="settingAlign left<?
				if($arProp['ALIGN'] == 'left'){
					?> check<?
				}
				?>" title="Слево" onclick="checkBox(this, 'left', <?=$i?>)"></a><a class="settingAlign center<?
				if($arProp['ALIGN'] == 'center'){
					?> check<?
				}
				?>" title="По центру" onclick="checkBox(this, 'center', <?=$i?>)"></a><a class="settingAlign right<?
				if($arProp['ALIGN'] == 'right'){
					?> check<?
				}
				?>" title="Справо" onclick="checkBox(this, 'right', <?=$i?>)"></a></td>
			</tr><?
			$i++;
		}
		?>
		</tbody>
	</table>
	<div style="padding-top:5px"><label><input type="checkbox" name="PREVIEW" value="1"<?
	if($arSSetting['PREVIEW']){
		?> checked="checked"<?
	}
	?>>Показать картинки вместо названий</label></div><?
	if($USER['USER_GROUP'] == 1){
		?><div><label><input type="checkbox" name="DEFAULT" value="1"<?
		if($arSSetting['DEFAULT']){
			?> checked="checked"<?
		}
		?>>По умолчанию для всех пользователей</label></div><?
	}
	if($arSSetting){
		?><div><a href="setting-reset.php">Сбросить настройки</a></div><?
	}
	?><input type="submit" class="sub rightSub" value="Сохранить">
</form><?
?>