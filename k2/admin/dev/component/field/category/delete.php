<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');

if(!$arField = $LIB['FIELD']->ID($_ID)){
	Redirect('/k2/admin/component/block/');
}
$nBlock = preg_replace("#k2_component(\d+)category#", "\\1", $arField['TABLE']);

$LIB['FIELD']->Delete($_ID);
Redirect('/k2/admin/dev/component/field/category/?id='.$nBlock);

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>