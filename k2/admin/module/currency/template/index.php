<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'CURRENCY');
tab(array(array('Модули', '/module/'), array('Курс валют', '/module/currency/', 1)));
tab_(array(array('Настройки', '/module/currency/'), array('Шаблоны', '/module/currency/template/', 1)));

$arModule = $LIB['MODULE']->ID('CURRENCY');
$arAllTemplate = templateList('CURRENCY');

$arField['CONTROLLER'] = array(
'INDEX' => 'Контроллер'
);
$arField['TEMPLATE'] = array(
'INDEX' => 'Шаблон'
);

if($_GET['template']){
	for($i=0; $i<count($arAllTemplate); $i++)
	{
		if($arAllTemplate[$i]['TEMPLATE'] == $_GET['template']){
			$arTemplate = $arAllTemplate[$i];
			break;
		}
	}
	if(!$arTemplate){		Redirect('?template=default');
	}
}else{
	Redirect('?template='.$arAllTemplate[0]['TEMPLATE']);
}

if($_POST){
	foreach($arField as $sType => $arList)
	{
		foreach($arList as $sKey => $sName)
		{
			if(!$LIB['FILE']->Edit('/k2/module/currency/template/'.$arTemplate['TEMPLATE'].'/'.strtolower($sType).'/'.strtolower($sKey).'.php', $_POST[$sType][$sKey])){
		    	if($LIB['FILE']->Error){
					$LIB['MODULE']->Error = $LIB['FILE']->Error;
				}
		    	break;
			}
		}

	}
	if(!$LIB['MODULE']->Error){
		Redirect('/k2/admin/module/currency/template/?template='.$arTemplate['TEMPLATE'].'&complite=1');
	}
}else{
	foreach($arField as $sType => $arList)
	{
		foreach($arList as $sKey => $sName)
		{
			$_POST[$sType][$sKey] = $LIB['FILE']->Read('/k2/module/currency/template/'.$arTemplate['TEMPLATE'].'/'.strtolower($sType).'/'.strtolower($sKey).'.php');
		}
	}
}

for($i=0; $i<count($arAllTemplate); $i++)
{
	$arTab[] = array($arAllTemplate[$i]['NAME'], '/module/currency/template/?template='.$arAllTemplate[$i]['TEMPLATE'], ($arAllTemplate[$i]['TEMPLATE'] == $arTemplate['TEMPLATE']));
}

if($arTemplate['TEMPLATE'] != 'default'){
	$arAction[] = '<a href="/k2/admin/module/template-delete.php?module=CURRENCY&template='.$arTemplate['TEMPLATE'].'" onclick="return $.prompt(this)" style="color:red">Удалить шаблон</a>';
}else{
	$arAction[] = '<a href="#" onclick="$.layer({\'get\':\'/k2/admin/module/template-copy.php?module=CURRENCY\', title:\'Скопировать шаблон\', w:398}, function(){k2.template.copy();})">Скопировать шаблон</a>';
}

tab_($arTab, $arAction, 'subMenu subMenu_');

?><div class="content">
    <form action="?template=<?=$arTemplate['TEMPLATE']?>" method="post" enctype="multipart/form-data" class="form">
    	<?
    	formError($LIB['MODULE']->Error);
    	if($_TEMPLATE == 'default'){
			if($LIB['MODULE']->Error || $_GET['complite']){				?><p></p><?
			}
			?><div class="warning">Шаблон по умолчанию представлен только для ознакомительных целей, если требуется внести изменения скопируйте шаблон</div><?
		}
        foreach($arField as $sType => $arList)
		{
			foreach($arList as $sKey => $sName)
			{
				?><div class="item">
					<div class="name"><?=$sName?></div>
					<div class="field"><textarea name="<?=$sType?>[<?=$sKey?>]" cols="40" rows="10" data-code="true"><?=html($_POST[$sType][$sKey])?></textarea></div>
				</div><?
			}
		}
    	?><div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>