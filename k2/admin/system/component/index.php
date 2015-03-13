<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/header.php');

$arComponent = $LIB['COMPONENT']->ID($_COMPONENT);
if(!$arComponent){
	exit;
}

if(!$DB->Row("SELECT 1 FROM `k2_component".$arComponent['ID']."` WHERE `COLLECTION` = '".$_COLLECTION."' AND `CATEGORY` = '".$_CATEGORY."' LIMIT 1") && $arComponent['CATEGORY']){
	Redirect('/k2/admin/system/component/category/?field='.$_FIELD.'&component='.$_COMPONENT.'&collection='.$_COLLECTION.'&category='.$_CATEGORY);
}else{
	Redirect('/k2/admin/system/component/element/?field='.$_FIELD.'&component='.$_COMPONENT.'&collection='.$_COLLECTION.'&category='.$_CATEGORY);
}

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/footer.php');
?>