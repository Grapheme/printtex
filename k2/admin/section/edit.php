<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION');

if(!$arSection = $LIB['SECTION']->ID($_SECTION, 1)){
	Redirect('/k2/admin/');
}
if($_POST){
	if($nID = $LIB['SECTION']->Edit($_SECTION, $_POST, 1)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?section='.$_SECTION.'&complite=1');
		}else{
			Redirect('/k2/admin/section/');
		}
	}
}else{
	$_POST = $arSection;
}
$arDesign = $LIB['DESIGN']->Rows();
$arBlock = $LIB['BLOCK']->Rows();

tab(array(array('Раздел', '/section/edit.php?section='.$_SECTION, 1), array('Наполнение', '/section/content/?section='.$_SECTION)));
tab_(array(array('Настройки', '/section/edit.php?section='.$_SECTION, 1), array('Функционал', '/section/block/?section='.$_SECTION), array('Права доступа', '/section/permission.php?section='.$_SECTION)));

$arSite = $LIB['SITE']->ID($arSection['SITE']);
$nInheritDegign = $arSite['DESIGN'];
$arBack = $LIB['SECTION']->Back(array('ID' => $arSection['ID']));
if($arBack[0]['ID'] != $arSection['ID'] && $arBack[0]['DESIGN']){	$nInheritDegign = $arBack[0]['DESIGN'];
}
for($i=0; $i<count($arDesign); $i++)
{
	if($nInheritDegign == $arDesign[$i]['ID']){
		$sInheritDegign = $arDesign[$i]['ID'].'.'.html($arDesign[$i]['NAME']);
	}
}

?><div class="content">
	<h1>Редактирование</h1>
    <form method="post" enctype="multipart/form-data" class="form">
    	<?formError($LIB['SECTION']->Error)?>
        <div class="item">
			<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
			if($_POST['ACTIVE']){
				?> checked<?
			}
			?>>Активность</label>
		</div>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>" id="transcription-from"></div>
		</div>
		<div class="item">
			<div class="name">Папка<span class="star">*</span></div>
			<div class="field"><input type="text" name="FOLDER" value="<?=html($_POST['FOLDER'])?>" id="transcription-to"><a href="#" onclick="return k2.transcription.lock(this)" id="transcription-icon" class="icon <?
		    if($_COOKIE['K2_TRANSCRIPTION']){
		    	?>lock" title="Автоматический перевод отключен"<?
		    }else{
		    	?>unlock" title="Автоматический перевод включен"<?
		    }
		    ?>></a></div>
		</div>
        <div class="item">
			<div class="name">Прямая ссылка</div>
			<div class="field"><input type="text" name="EXTERNAL" value="<?=html($_POST['EXTERNAL'])?>"></div>
		</div>
        <div class="item">
			<div class="name">Шаблон дизайна<span class="star">*</span></div>
			<div class="field"><select name="DESIGN"><option value="0">Наследовать (<?=$sInheritDegign;?>)</option><?
			for($i=0; $i<count($arDesign); $i++)
			{
				?><option value="<?=$arDesign[$i]['ID']?>"<?
				if($_POST['DESIGN'] == $arDesign[$i]['ID']){
					?> selected<?
				}
				?>><?=$arDesign[$i]['ID']?>. <?=html($arDesign[$i]['NAME'])?></option><?
			}
			?></select></div>
		</div><?
		$arField = array_merge($LIB['FIELD']->Rows('k2_section'), $LIB['FIELD_SEPARATOR']->Rows('k2_section'));
		for($i=0; $i<count($arField); $i++)
		{
	        if(!$i){
	        	usort($arField, 'sortArray');
	        }
	        if(!$arField[$i]['FIELD']){
	        	?><div class="fieldGroup">
	        		<div class="title"><?=$arField[$i]['NAME']?></div>
	        	</div><?
	        }else{
	       		echo $LIB['FORM']->Element($arField[$i]['ID'], '<div class="item"><div class="name">%NAME%</div><div class="field">%FIELD%</div></div>');
	        }
		}
		?>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/">отменить</a></p>
		</div>
		<script type="text/javascript">
		$(function(){
			k2.transcription.init();
		});
		</script>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>