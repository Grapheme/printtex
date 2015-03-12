<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');

$LIB['DESIGN']->Delete($_ID);
Redirect('/k2/admin/dev/design');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>