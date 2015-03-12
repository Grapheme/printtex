<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/header.php');

$DB->Query("DELETE FROM `k2_component_field` WHERE `COMPONENT` = '".$_COMPONENT."' AND `FIELD` = '".$_FIELD."' AND `COLLECTION` = '".$_COLLECTION."'");
$DB->Query("INSERT INTO `k2_component_field` (`COMPONENT`, `FIELD`, `COLLECTION`) VALUES ('".$_COMPONENT."', '".$_FIELD."', '".$_COLLECTION."')");

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/footer.php');
?>