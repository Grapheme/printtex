<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

include_once('header.php');

$LIB['COMPONENT_CATEGORY']->Delete($_ID, $_COMPONENT);
Redirect('/k2/admin/system/component/index.php?field='.$_FIELD.'&component='.$_COMPONENT.'&collection='.$_COLLECTION.'&category='.$_CATEGORY);

include_once('footer.php');
?>