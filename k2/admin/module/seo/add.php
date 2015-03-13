<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SEO');
tab(array(array('Модули', '/module/'), array('SEO', '/module/seo/', 1)));

if($_POST){
	if($nID = $MOD['SEO']->Add($_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/module/seo/');
		}
	}
}
?><div class="content">
	<h1>Добавление</h1>
    <form action="add.php" method="post" class="form">
    	<?formError($MOD['SEO']->Error)?>
        <div class="item">
			<div class="name">Путь<span class="star">*</span></div>
			<div class="field"><input type="text" name="PAGE" value="<?=html($_POST['PAGE'])?>" autofocus></div>
		</div>
		<div class="item">
			<div class="name">Заголовок</div>
			<div class="field"><input type="text" name="TITLE" value="<?=html($_POST['TITLE'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Ключевые слова</div>
			<div class="field"><input type="text" name="KEYWORD" value="<?=html($_POST['KEYWORD'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Описание</div>
			<div class="field"><textarea name="DESCRIPTION" cols="40" rows="6" data-code="true"><?=html($_POST['DESCRIPTION'])?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/module/seo/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>