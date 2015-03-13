<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');

$LIB['FIELD']->Delete($_ID);
Redirect('/k2/admin/dev/field/section/');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>