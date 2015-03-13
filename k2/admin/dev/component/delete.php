<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
$LIB['COMPONENT']->Delete($_ID);
Redirect('/k2/admin/dev/component/');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>