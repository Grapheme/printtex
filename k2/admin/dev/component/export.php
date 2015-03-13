<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('TEMPLATE');

if(!($arComponent = $LIB['COMPONENT']->ID($_ID)) || (!$sFile = $LIB['COMPONENT']->Export($_ID))){
	Redirect('/k2/admin/dev/component/');
}

$sContent = file_get_contents($sFile, "rb");
header('HTTP/1.0 200 OK');
header('Cache-Control: None');
header('Last-Modified: '.date('D, d M Y H:i:s \G\M\T'));
header('Pragma: no-cache');
header('Accept-Ranges: bytes');
header('Content-Disposition: attachment; filename="k2component['.$arComponent['ID'].']'.strtolower(substr(fileTranslation(str_replace(' ', '_', $arComponent['NAME'])), 0, 30)).'.zip"');
header('Content-Length: '.mb_strlen($sContent));
header('Content-Type: application/octet-stream');
header('Proxy-Connection: close');
header('');
echo $sContent;
@unlink($sZipFile);
?>