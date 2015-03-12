<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/component/class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/component/class.group.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/component/class.category.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/component/class.element.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/component/function.php');
$LIB['COMPONENT'] = new Component();
$LIB['COMPONENT_GROUP'] = new ComponentGroup();
$LIB['COMPONENT_ELEMENT'] = new ComponentElement();
$LIB['COMPONENT_CATEGORY'] = new ComponentCategory();
?>