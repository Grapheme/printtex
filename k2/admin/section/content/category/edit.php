<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

tab(array(array('Раздел', '/section/edit.php?section='.$_SECTION), array('Наполнение', '/section/content/?section='.$_SECTION, 1)));

$arSBlock = $LIB['SECTION_BLOCK']->ID($_SECTION_BLOCK);
$arSection = $LIB['SECTION']->ID($arSBlock['SECTION']);
$arBlock = $LIB['BLOCK']->ID($arSBlock['BLOCK'], 1);
$arCategory = $LIB['BLOCK_CATEGORY']->ID($_ID, $arBlock['ID']);

if(!$arSBlock || !$arSection || !$arBlock || !$arCategory){
	Redirect('/k2/admin/');
}

$arSBlockList = $LIB['SECTION_BLOCK']->Rows($arSection['ID']);
$arTab = array();
for($i=0; $i<count($arSBlockList); $i++)
{
	$arTab[] = array($arSBlockList[$i]['NAME'], '/section/content/?section='.$_SECTION.'&section_block='.$arSBlockList[$i]['ID'], ($_SECTION_BLOCK == $arSBlockList[$i]['ID']));
}
if($arTab){
	tab_($arTab);
}

if($_CATEGORY){
	if($arRows = $LIB['BLOCK_CATEGORY']->Back(array('BLOCK' => $arBlock['ID'], 'ID' => $_CATEGORY))){
		for($i=0; $i<count($arRows); $i++)
		{
			$arNav[] = array($arRows[$i]['NAME'], '/section/content/?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$arRows[$i]['ID']);
		}
		navBack($arNav);
	}
}

if($_POST){
	if($nID = $LIB['BLOCK_CATEGORY']->Edit($_ID, $_SECTION_BLOCK, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$_CATEGORY.'id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/section/content/category/?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$_CATEGORY);
		}
	}
}else{
	$_POST = $arCategory;
}

?><div class="content">
	<h1>Редактирование</h1>
    <form method="post" enctype="multipart/form-data" class="form">
    	<?formError($LIB['BLOCK_CATEGORY']->Error)?>
    	<div class="item">
			<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
			if($_POST['ACTIVE']){
				?> checked<?
			}
			?>>Активность</label>
		</div>
        <div class="item">
			<div class="name">Сортировка<span class="star">*</span></div>
			<div class="field"><input type="text" name="SORT" value="<?=html($_POST['SORT'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div><?
		$arField = array_merge($LIB['FIELD']->Rows('k2_block'.$arBlock['ID'].'category'), $LIB['FIELD_SEPARATOR']->Rows('k2_block'.$arBlock['ID'].'category'));
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
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/section/content/?section=<?=$arSection['ID']?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>