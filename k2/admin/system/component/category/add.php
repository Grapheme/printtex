<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/header.php');

if($_CATEGORY){
	if($arCategory = $LIB['COMPONENT_CATEGORY']->Back(array('COMPONENT' => $_COMPONENT, 'ID' => $_CATEGORY))){
		$arNav[] = array('Компонент', '/system/component/?field='.$_FIELD.'&component='.$_COMPONENT.'&collection='.$_COLLECTION);
		for($i=0; $i<count($arCategory); $i++)
		{
			$arNav[] = array($arCategory[$i]['NAME'], '/system/component/?field='.$_FIELD.'&component='.$_COMPONENT.'&collection='.$_COLLECTION.'&category='.$arCategory[$i]['ID']);
		}
		navBack($arNav);
	}
}

if($_POST){
	$_POST['PARENT'] = $_CATEGORY;
	if($nID = $LIB['COMPONENT_CATEGORY']->Add($_COMPONENT, $_COLLECTION, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php'.$sURL.'&id='.$nID);
		}else{
			Redirect('index.php'.$sURL);
		}
	}
}else{
	$_POST['ACTIVE'] = 1;
}

?><div class="content">
	<h1>Добавление</h1>
    <form method="post" enctype="multipart/form-data" class="form">
    	<?formError($LIB['COMPONENT_CATEGORY']->Error)?>
    	<div class="item">
			<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
			if($_POST['ACTIVE']){
				?> checked<?
			}
			?>>Активность</label>
		</div><?
		if(!isset($_POST['SORT'])){
	    	$_POST['SORT'] = 10;
	    	if($arContent = $DB->Row("SELECT `SORT` FROM `k2_component".$_COMPONENT."category` WHERE `COLLECTION` = '".$_COLLECTION."' ORDER BY `ID` DESC LIMIT 1")){
	    		$_POST['SORT'] = $arContent['SORT'] + 10;
	    	}
	    }
		?>
        <div class="item">
			<div class="name">Сортировка<span class="star">*</span></div>
			<div class="field"><input type="text" name="SORT" value="<?=html($_POST['SORT'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div><?
		$arField = array_merge($LIB['FIELD']->Rows('k2_component'.$_COMPONENT.'category'), $LIB['FIELD_SEPARATOR']->Rows('k2_component'.$_COMPONENT.'category'));
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
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="index.php<?=$sURL?>">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/footer.php');
?>