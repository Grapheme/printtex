<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Функциональные блоки', '/dev/block/'), array('Компоненты', '/dev/component/'), array('Шаблоны навигации', '/dev/nav/', 1), array('Макеты дизайна', '/dev/design/'), array('Поля', '/dev/field/')));
if(!$arNav = $LIB['NAV']->ID($_ID)){
	Redirect('/k2/admin/nav/');
}
if($_POST){
	if($nID = $LIB['NAV']->Edit($_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/nav/');
		}
	}
}else{
	$_POST = $arNav;
}
?><div class="content">
	<h1>Редактирование</h1>
    <form action="edit.php?id=<?=$_ID?>" method="post" class="form">
    	<?formError($LIB['NAV']->Error)?>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Шаблон</div>
			<div class="field"><textarea name="TEMPLATE" cols="40" rows="6" data-code="true"><?=html($_POST['TEMPLATE'])?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/nav/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>