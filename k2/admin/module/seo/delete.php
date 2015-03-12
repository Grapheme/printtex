<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SEO');

$MOD['SEO']->Delete($_ID);
Redirect('/k2/admin/module/seo/');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>