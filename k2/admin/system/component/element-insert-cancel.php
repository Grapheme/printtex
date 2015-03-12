<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/function.php');
permissionCheck('SECTION_CONTENT');

$DB->Query("DELETE FROM `k2_user_setting` WHERE `USER` = '".$USER['ID']."' AND `ACTION` = 'ELEMENT_MOVE';");
Redirect('/k2/admin/section/content/?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$_CATEGORY);
?>