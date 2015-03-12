<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Функциональные блоки', '/dev/block/'), array('Компоненты', '/dev/component/', 1), array('Шаблоны навигации', '/dev/nav/'), array('Макеты дизайна', '/dev/design/'), array('Поля', '/dev/field/')));
if(!$LIB['COMPONENT_GROUP']->Rows()){
	Redirect('/k2/admin/dev/component/');
}
if($_POST){
    $_POST['FILE'] = $_FILES['FILE'];
    if($nID = $LIB['COMPONENT']->Import($_POST)){
	    Redirect('/k2/admin/dev/component/edit.php?id='.$nID);
	}
}
?>
<div class="content">
	<h1>Импорт</h1>
	<form method="post" enctype="multipart/form-data" class="form">
		<?formError($LIB['COMPONENT']->Error)?>
		<div class="item">
			<div class="name">Группа<span class="star">*</span></div>
			<div class="field"><select name="COMPONENT_GROUP"><?
			$arGroup = $LIB['COMPONENT_GROUP']->Rows();
			for($i=0; $i<count($arGroup); $i++)
			{
				?><option value="<?=$arGroup[$i]['ID']?>"<?
				if($_POST['COMPONENT_GROUP'] == $arGroup[$i]['ID']){
					?> selected<?
				}
				?>><?=$arGroup[$i]['NAME']?></option><?
			}
			?></select></div>
		</div>
		<div class="item">
			<div class="name">Файл<span class="star">*</span></div>
			<div class="field"><input type="file" name="FILE"></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/component/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>