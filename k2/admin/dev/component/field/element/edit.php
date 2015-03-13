<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');

if(!$arField = $LIB['FIELD']->ID($_ID)){
	Redirect('/k2/admin/dev/block/');
}
$nBlock = preg_replace("#k2_component(\d+)#", "\\1", $arField['TABLE']);

tab(array(array('Функциональные блоки', '/dev/block/'), array('Компоненты', '/dev/component/', 1), array('Шаблоны навигации', '/dev/nav/'), array('Макеты дизайна', '/dev/design/'), array('Поля', '/dev/field/')));
tab_(array(array('Настройки', '/dev/component/edit.php?id='.$nBlock), array('Поля категорий', '/dev/component/field/category/?id='.$nBlock), array('Поля элементов', '/dev/component/field/element/?id='.$nBlock, 1)));

if($_POST){
	if($nID = $LIB['FIELD']->Edit($_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$_ID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/component/field/element/?id='.$nBlock);
		}
	}
}else{
	$_POST = $arField;
}

?><div class="content">
	<h1>Редактирование</h1>
	<form action="edit.php?id=<?=$_ID?>" method="post" class="form">
    	<?formError($LIB['FIELD']->Error)?><?
    	$arFieldType = $LIB['FIELD']->Type();
    	?><p>Тип: <b><?=$arFieldType[$arField['TYPE']]?></b></p>
	    <p>Поле: <b><?=html($arField['FIELD'])?></b>
        <div class="item">
			<div class="name">Описание<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div><?
	    include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/dev/field/type/'.(int)$arField['TYPE'].'.php');
		?><div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/component/field/element/?id=<?=$nBlock?>">отменить</a></p>
		</div>
	</form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>