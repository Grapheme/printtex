<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');

$LIB['NAV']->Delete($_ID);
Redirect('/k2/admin/dev/nav/');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>