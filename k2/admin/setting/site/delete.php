<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');

$LIB['SITE']->Delete($_ID);
Redirect('/k2/admin/setting/site/');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>