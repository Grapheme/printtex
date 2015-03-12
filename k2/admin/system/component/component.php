<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');

if($arComponent = $LIB['COMPONENT']->Rows()){
    if($_COMPONENT){
    	?><iframe src="/k2/admin/system/component/?field=<?=$_FIELD?>&component=<?=$_COMPONENT?>&collection=<?=$_COLLECTION?>" width="100%" height="100%" frameborder="0" name="ifr" id="ifr" style="min-height:400px"></iframe>
    	<div style="height:30px; padding-top:5px;">
			<input type="submit" class="sub rightSub" value="Вставить" onclick="k2.component.past({'field':<?=$_FIELD?>, 'component':<?=$_COMPONENT?>, 'collection':<?
			if(!$_COLLECTION){	            $arCollection = $DB->Row("SELECT `COLLECTION` FROM `k2_component".$_COMPONENT."` ORDER BY `COLLECTION` DESC LIMIT 1");
				$arCollection_ = $DB->Row("SELECT `COLLECTION` FROM `k2_component".$_COMPONENT."category` ORDER BY `COLLECTION` DESC LIMIT 1");
				$_COLLECTION = max(array($arCollection['COLLECTION'], $arCollection_['COLLECTION']));
				$_COLLECTION += 1;
			}
			echo $_COLLECTION;
			?>})">
		</div><?
    }else{    	?><div class="form">
	    	<div class="item">
				<div class="name">Выберите компонент</div>
				<div class="field"><select name="component" id="setComponent"><?
		   		for($i=0; $i<count($arComponent); $i++)
		   		{
		   			?><option value="<?=$arComponent[$i]['ID']?>"><?=$arComponent[$i]['NAME']?></option><?
		   		}
		   		?></select></div>
			</div>
    	</div>
		<div style="height:30px;">
			<input type="submit" class="sub rightSub" value="Продолжить" onclick="k2.component.set(<?=$_FIELD?>)">
		</div><?
    }
}else{
	?><div style="height:100px; text-align:center; line-height:100px;">Нет доступных компонентов</div><?
}
?>