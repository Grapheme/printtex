<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Функциональные блоки', '/dev/block/'), array('Компоненты', '/dev/component/'), array('Шаблоны навигации', '/dev/nav/'), array('Макеты дизайна', '/dev/design/'), array('Поля', '/dev/field/', 1)));
tab_(array(array('Для сайта', '/dev/field/site/'), array('Для разделов', '/dev/field/section/'), array('Для пользователей', '/dev/field/user/', 1)));
if($_POST){
	if($nID = $LIB['FIELD']->Add('k2_user', $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/field/user/');
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
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/field/user/">отменить</a></p>
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