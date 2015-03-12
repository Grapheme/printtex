<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');

permissionCheck('SECTION_CONTENT');

if(!$arComponent = $LIB['COMPONENT']->ID($_COMPONENT, 1)){
	exit;
}
if(!$LIB['FIELD']->ID($_FIELD)){
	exit;
}

if(!$_COLLECTION){
	$arCollection = $DB->Row("SELECT `COLLECTION` FROM `k2_component".$_COMPONENT."` ORDER BY `COLLECTION` DESC LIMIT 1");
	$arCollection_ = $DB->Row("SELECT `COLLECTION` FROM `k2_component".$_COMPONENT."category` ORDER BY `COLLECTION` DESC LIMIT 1");
	$_COLLECTION = max(array($arCollection['COLLECTION'], $arCollection_['COLLECTION']));
	$_COLLECTION += 1;
}

$sURL = urlQuery(array('collection' => $_COLLECTION, 'category' => $_CATEGORY));
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="/k2/admin/jc/style.css">
        <!--[if IE]><link rel="stylesheet" type="text/css" href="/k2/admin/jc/ie.css"><![endif]-->
        <script type="text/javascript" src="/k2/admin/jc/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/k2/admin/jc/jquery-ui-1.8.17.custom.min.js"></script>
		<script type="text/javascript" src="/k2/admin/jc/jquery.plugin.js"></script>
		<script type="text/javascript" src="/k2/admin/jc/jquery.layer.js"></script>
		<script type="text/javascript" src="/k2/admin/jc/jquery.MultiFile.pack.js"></script>
		<script type="text/javascript" src="/k2/admin/jc/java.js"></script>
		<script type="text/javascript" src="/k2/admin/tinymce/jquery.tinymce.js"></script>
		<script type="text/javascript" src="/k2/admin/tinymce/init.js"></script>
		<title>K2CMS</title>
		<style>
		html, body{			background:#fff;		};
		</style>
	</head>
<body>