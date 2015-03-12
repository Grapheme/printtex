<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

if(!$arField = $LIB['FIELD']->ID($_ID)){
	Redirect('/k2/admin/module/shop/address/');
}

tab(array(array('Модули', '/module/'), array('Интернет-магазин', '/module/shop/', 1)));
tab_(array(
	array('Заказы', '/module/shop/'),
	array('Плательщики', '/module/shop/payer/'),
	array('Адреса', '/module/shop/address/', 1),
	array('Статусы', '/module/shop/status/'),
	array('Оплата', '/module/shop/payment/'),
	array('Доставка', '/module/shop/delivery/'),
	array('Обработчики', '/module/shop/handler/')
));

if($_POST){
	if($nID = $LIB['FIELD']->Edit($_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$_ID.'&complite=1');
		}else{
			Redirect('/k2/admin/module/shop/address/');
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
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/module/shop/address/">отменить</a></p>
		</div>
	</form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>