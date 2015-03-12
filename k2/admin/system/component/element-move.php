<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/function.php');
permissionCheck('SECTION_CONTENT');

$DB->Query("DELETE FROM `k2_user_setting` WHERE ACTION = 'ELEMENT_MOVE' AND USER = '".$USER['ID']."'");

$DB->Query("
INSERT INTO `k2_user_setting` (
	`USER`,
	`ACTION`,
	`DATA`
)VALUES(
	'".$USER['ID']."', 'ELEMENT_MOVE', '".DBS(serialize(array('ID' => $_ID, 'BLOCK' => $_BLOCK)))."'
);");

Redirect($_SERVER['HTTP_REFERER'].'&action=complite_element_move');
?>