<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Функциональные блоки', '/dev/block/', 1), array('Компоненты', '/dev/component/'), array('Шаблоны навигации', '/dev/nav/'), array('Макеты дизайна', '/dev/design/'), array('Поля', '/dev/field/')));
tab_(array(array('Настройки', '/dev/block/edit.php?id='.$_ID), array('Поля категорий', '/dev/block/field/category/?id='.$_ID, 1), array('Поля элементов', '/dev/block/field/element/?id='.$_ID)));
if(!$arBlock = $LIB['BLOCK']->ID($_ID)){
	Redirect('/k2/admin/dev/block/');
}
if($_POST){
	if($nID = $LIB['FIELD']->Add('k2_block'.$_ID.'category', $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/block/field/category/?id='.$_ID);
		}
	}
}
?><div class="content">
	<h1>Добавление</h1>
    <form action="add.php?id=<?=$_ID?>&amp;TYPE=<?=(int)$_REQUEST['TYPE']?>" method="post" class="form">
    	<?formError($LIB['FIELD']->Error)?>
        <div class="item">
			<div class="name">Тип<span class="star">*</span></div>
			<div class="field"><select name="TYPE" id="type-field"><?
			$arFieldType = $LIB['FIELD']->Type();
			foreach($arFieldType as $nKey=>$nValue)
			{
				if($nKey == 3 && !$LIB['SELECT']->Rows()){
					continue;
				}
				?><option value="<?=$nKey?>"<?
				if($nKey == $_REQUEST['TYPE']){
					?> selected="selected"<?
				}
				?>><?=$nValue?></option><?
			}
			?></select></div>
		</div>
        <div class="item">
			<div class="name">Название поля<span class="star">*</span></div>
			<div class="field"><input type="text" name="FIELD" value="<?=html($_POST['FIELD'])?>"></div>
		</div>
        <div class="item">
			<div class="name">Описание<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div>
		<?
		include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/dev/field/type/'.(int)$_REQUEST['TYPE'].'.php');
		?>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/block/field/category/?id=<?=$_ID?>">отменить</a></p>
		</div>
    </form>
    <script type="text/javascript">
	$(function(){
		$('#type-field').change(function(){
			location.href = 'add.php?id=<?=$_ID?>&TYPE='+$(this).val();
			return false;
		});
	});
	</script>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>