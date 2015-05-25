<?
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/function.php');

header('Pragma: no-cache');

if($SETTING['DEBUG_PANEL'] && ($USER['USER_GROUP'] == 1)){
	$GLOBALS['DEBUG']['START'] = microtime(1);
}

$CURRENT = array();

$CURRENT['ACTION'] = 'index';
$arParse = parse_url($_GET['PATH']);
if(preg_match("#(.*?)/(\d+)/(\d+)(/)?$#i", $arParse['path'], $arMath)){
	$arPath = pathinfo($arMath[1]);
	$CURRENT['ACTION'] = 'full';
	$arPath['SECTION_BLOCK'] = $arMath[2];
	$arPath['ELEMENT'] = $arMath[3];
}else{
   	$arPath = pathinfo($_GET['PATH']);
}
if(!$arPath['extension']){
	$arPath['URL'] = $arPath['dirname'].'/'.$arPath['basename'].'/';
}else{
	$arPath['URL'] = $arPath['dirname'].'/'.$arPath['filename'].'.'.$arPath['extension'];
}
$arPath['URL'] = str_replace(array("\\", "//"), array("", "/"), $arPath['URL']);


$arSite = $LIB['SITE']->Rows();
for($i=0; $i<count($arSite); $i++)
{
	if($arSite[$i]['DOMAIN'] == $_SERVER['SERVER_NAME']){
        $CURRENT['SITE'] = $arSite[$i];
		break;
	}
	if($arSite[$i]['ALIAS']){
		$arExp = explode("\n", $arSite[$i]['ALIAS']);
		for($j=0; $j<count($arExp); $j++)
		{
			if(str_replace(array("\r", "\n"), array('', ''), $arExp[$j]) == $_SERVER['SERVER_NAME']){
				$CURRENT['SITE'] = $arSite[$i];
				$bFind = true;
				break;
			}
		}
		if($bFind){
			break;
		}
	}
}

if(!$CURRENT['SITE']){
	$CURRENT['SITE'] = $arSite[0];
}

$CURRENT['DESIGN'] = $LIB['DESIGN']->ID($CURRENT['SITE']['DESIGN']);

if($arPath['URL'] == '/'){
	header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
	$bHTTPStatus = 200;
	$CURRENT['SECTION'] = $LIB['SECTION']->ID($CURRENT['SITE']['SECTION_INDEX']);
	$CURRENT['PATH'] = $CURRENT['SECTION']['URL'];
}elseif($arSection = $DB->Row("SELECT * FROM `k2_section` WHERE `SITE` = '".$CURRENT['SITE']['ID']."' AND `URL` = '".DBS($arPath['URL'])."'")){
	$CURRENT['SECTION'] = $arSection;
	if($CURRENT['ACTION'] == 'full'){
   		if(
   		!($CURRENT['SECTION_BLOCK'] = $DB->Row("SELECT * FROM `k2_section_block` WHERE `ID` = '".$arPath['SECTION_BLOCK']."' ORDER BY `SORT` ASC")) ||
   		!($CURRENT['SECTION_BLOCK']['SECTION'] == $CURRENT['SECTION']['ID']) ||
   		!($CURRENT['BLOCK'] = $LIB['BLOCK']->ID($CURRENT['SECTION_BLOCK']['BLOCK'])) ||
   		!($CURRENT['ELEMENT'] = $DB->Row("SELECT ID FROM `k2_block".$CURRENT['SECTION_BLOCK']['BLOCK']."` WHERE `ID` = '".$arPath['ELEMENT']."'"))
   		){
        	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			$bHTTPStatus = 404;
			$CURRENT['SECTION'] = $LIB['SECTION']->ID($CURRENT['SITE']['SECTION_NOT_FOUND']);
			$CURRENT['PATH'] = $CURRENT['SECTION']['URL'];
   		}else{
   			$CURRENT['PATH'] = $CURRENT['SECTION']['URL'].$CURRENT['SECTION_BLOCK']['ID'].'/'.$CURRENT['ELEMENT']['ID'].'/';
   		}
	}else{
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		$bHTTPStatus = 200;
        $CURRENT['PATH'] = $CURRENT['SECTION']['URL'];
	}
}else{
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
	$bHTTPStatus = 404;
	$CURRENT['SECTION'] = $LIB['SECTION']->ID($CURRENT['SITE']['SECTION_NOT_FOUND']);
	$CURRENT['PATH'] = $CURRENT['SECTION']['URL'];
}

if($CURRENT['SECTION']['EXTERNAL'] && ($CURRENT['SECTION']['EXTERNAL'] != $CURRENT['SECTION']['URL'])){
	Redirect($CURRENT['SECTION']['EXTERNAL'], 301);
}

if($CURRENT['SECTION']['DESIGN_SHOW'] != $CURRENT['DESIGN']['ID']){	$CURRENT['DESIGN'] = $LIB['DESIGN']->ID($CURRENT['SECTION']['DESIGN_SHOW']);
}

if(!$CURRENT['SITE']['ACTIVE'] && ($USER['USER_GROUP'] != 1)){
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/site-off.php');
	exit;
}

include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/before.php');

if($CURRENT['DESIGN']['ID']){
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/'.$CURRENT['DESIGN']['ID'].'/header.php');
}
$nPermission = 1;
if($USER['USER_GROUP'] != 1){
    if($USER['PERMISSION']['SECTION'][$CURRENT['SECTION']['ID']]){
    	$nPermission = $USER['PERMISSION']['SECTION'][$CURRENT['SECTION']['ID']];
    }elseif($CURRENT['SECTION']['PERMISSION']){
    	$nPermission = $CURRENT['SECTION']['PERMISSION'];
    }elseif($USER['PERMISSION']['SITE'][$CURRENT['SITE']['ID']]){
    	$nPermission = $USER['PERMISSION']['SITE'][$CURRENT['SITE']['ID']];
    }elseif($CURRENT['SITE']['PERMISSION']){
    	$nPermission = $CURRENT['SITE']['PERMISSION'];
    }
}

if($nPermission != 4){
	if($CURRENT['ACTION'] == 'index' || $bHTTPStatus == 404){
	    $arSBlock = $DB->Rows("SELECT * FROM `k2_section_block` WHERE `SECTION` = '".$CURRENT['SECTION']['ID']."' AND `ACTIVE` = 1 ORDER BY `SORT` ASC");
	    foreach($arSBlock as $arSBlock_)
	    {
	    	$CURRENT['SECTION_BLOCK'] = $arSBlock_;
	    	$CURRENT['BLOCK'] = $LIB['BLOCK']->ID($arSBlock_['BLOCK']);
	    	Sandbox('/k2/dev/block/'.$arSBlock_['BLOCK'].'/template.php');
	    }
	}elseif($CURRENT['ACTION'] == 'full'){
	    Sandbox('/k2/dev/block/'.$CURRENT['BLOCK']['ID'].'/template-full.php');
	}
}else{
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/permission-denied.php');
}

if($CURRENT['DESIGN']['ID']){
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/'.$CURRENT['DESIGN']['ID'].'/footer.php');
}

include($_SERVER['DOCUMENT_ROOT'].'/k2/behind.php');
include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/after.php');

if($SETTING['DEBUG_PANEL'] && ($USER['USER_GROUP'] == 1)){
	?><link rel="stylesheet" type="text/css" href="/k2/admin/jc/debug.css">
	<div id="k2debug">
		<div>Страница сгенерирована за: <b><?=round(microtime(1)-$GLOBALS['DEBUG']['START'], 4)?></b> сек. Время генерации запросов: <b><?=$GLOBALS['DEBUG']['SQL_TIME']?></b> сек. Использовано запросов: <b><?=$GLOBALS['DEBUG']['SQL_COUNT']?></b> шт. <a href="javascript:void(0)" onclick="document.getElementById('k2debug-sql').style.display = 'block'; this.style.display = 'none';">Показать запросы</a></div>
		<div id="k2debug-sql">
			<table>
				<tr>
					<th>Запрос</th>
					<th>Время</th>
				</tr><?
				for($i=0; $i<count($GLOBALS['DEBUG']['SQL_QUERY']); $i++)
				{
					?><tr>
						<td align="left"><?=$GLOBALS['DEBUG']['SQL_QUERY'][$i]?></td>
						<td><?=$GLOBALS['DEBUG']['SQL_QUERY_TIME'][$i]?></td>
					</tr><?
				}
				?></table>
		</div>
	</div><?
}
?>