<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Функциональные блоки', '/dev/block/'), array('Компоненты', '/dev/component/', 1), array('Шаблоны навигации', '/dev/nav/'), array('Макеты дизайна', '/dev/design/'), array('Поля', '/dev/field/')));
if($_POST){
	if($nID = $LIB['COMPONENT']->Add($_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/component/');
		}
	}
}
?><div class="content">
	<h1>Добавление</h1>
    <form action="add.php" method="post" class="form">
    	<?formError($LIB['COMPONENT']->Error)?>
        <div class="item">
			<input type="hidden" name="ICON" value="default" id="componentIconValue">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>" autofocus><a href="#" onclick="$.layer({'get':'icon.php', 'title':'Иконка для компонента', w:355}, function(){k2.component.icon();})" title="Выбрать иконку"><img src="/k2/admin/i/component/<?=html($_POST['ICON'])?>.png" width="16" height="16" id="componentIcon"></a></div>
		</div>
		<div class="item">
			<div class="name">Группа<span class="star">*</span></div>
			<div class="field"><select name="COMPONENT_GROUP"><?
			$arGroup = $LIB['COMPONENT_GROUP']->Rows();
			for($i=0; $i<count($arGroup); $i++)
			{
				?><option value="<?=$arGroup[$i]['ID']?>"<?
				if($_POST['COMPONENT_GROUP'] == $arGroup[$i]['ID']){
					?> selected="selected"<?
				}
				?>><?=$arGroup[$i]['NAME']?></option><?
			}
			?></select></div>
		</div>
        <div class="item"><label><input type="checkbox" name="CATEGORY" value="1"<?
	    if($_POST['CATEGORY']){
	    	?> checked="checked"<?
	    }
	    ?>>Отображать категории</label></div>
		<div class="item">
			<div class="name">Шаблон</div>
			<div class="field"><textarea name="TEMPLATE" cols="40" rows="6" data-code="true"><?=html($_POST['TEMPLATE'])?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/component/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>