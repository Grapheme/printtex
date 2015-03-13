<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/user/class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/user/class.group.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/user/function.php');
$LIB['USER'] = new User();
$LIB['USER_GROUP'] = new UserGroup();
?>