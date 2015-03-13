<?
function Redirect($sPath = '/', $nStatus = 0)
{
    if($nStatus == 301){
    	header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
    	header('Location: '.$sPath, true, 301);
    	exit;
    }
    if($nStatus == 302){
    	header($_SERVER['SERVER_PROTOCOL'].' 302 Moved Permanently');
    	header('Location: '.$sPath, true, 302);
    	exit;
    }
    header('Location: '.$sPath);
    exit;
}

function dateFormat($sDate, $sTemplate = 'd.m.Y, H:i')
{
    $arMonth = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
	if(preg_match("#(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})#", $sDate, $arMath)){
        if($sDate != '0000-00-00 00:00:00'){
        	$sTemplate = str_replace('month', $arMonth[(int)$arMath[2]-1], $sTemplate);
        	$sDate = date($sTemplate, mktime($arMath[4], $arMath[5], $arMath[6], $arMath[2], $arMath[3], $arMath[1]));
        	return $sDate;
        }
    }
    return false;
}

function bufferContent($sCont)
{
	global $LIB, $DELAYED_VARIABLE;
	if($DELAYED_VARIABLE){
		foreach($DELAYED_VARIABLE as $sKey=>$sValue)
		{
			$sCont = str_replace('<!-- $'.$sKey.'$ -->', $sValue, $sCont);
		}
	}
	return $sCont;
}

function clearArray($arList = array())
{
	$arNewList = array();
	foreach($arList as $arList)
	{
		if($arList){
			$arNewList[] = $arList;
		}
	}
	return $arNewList;
}

function p($mVar)
{
	?><pre><?print_R($mVar);?></pre><?
}

function sendMail($sTo, $sFrom, $sBody, $sSubject = '')
{
    if(!$sSubject){
    	$sSubject = 'Информационное сообщение с сайта '.$_SERVER['HTTP_HOST'];
    }
    #$sBody = str_replace(array("'", '\"'), array("\'", '"'), trim($sBody));
    $sHead =
    "Content-Type: text/plain; charset=\"utf-8\"\nX-Mailer: PHP\n".
    "From: $sFrom\r\n".
    "Reply-To: $sFrom";
    return mail($sTo, $sSubject, $sBody, $sHead);
}

function sortArray($a, $b)
{
	if($a['SORT'] == $b['SORT']){
		return 0;
	}
	if($a['SORT'] < $b['SORT']){
		return -1;
	}
	return 1;
}

function urlContent($sURL)
{
	$arURL = parse_url($sURL);
	$sQuery = $arURL['path'];
	if($arURL['query']){
		$sQuery .= '?'.$arURL['query'];
	}
	if(!$sQuery){
		$sQuery = '/';
	}
	if(!($socket = fsockopen($arURL['host'], 80, $eCode, $eText, 5))){
		return false;
	}

	$content = '';
	$headers  = "GET ".$sQuery." HTTP/1.0\r\n"
	."Host: ".$arURL['host']."\r\n"
	."Referer: ".$arURL['scheme']."://".$arURL['host']."\r\n"
	."User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.6) Gecko/2009011913 MRA 5.3 (build 02557) Firefox/3.0.6\r\n"
	."Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
	."Accept-Language: ru,en-us;q=0.7,en;q=0.3\r\n"
	."Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7\r\n"
	."\r\n";
	fputs($socket, $headers);
	while(!feof($socket))
	{
		$content .= fgets($socket, 128);
	}
	fclose($socket);
	return preg_replace("#.*?(\x0D\x0A){2}(.*)#sm", "$2", $content);
}

function httpRequest($sHost, $arPar = array())
{
	if(!$arPar['METHOD']){
		$arPar['METHOD'] = 'GET';
	}
	if(!$arPar['URI']){
		$arPar['URI'] = '/';
	}

	$sQuery = '';
	if($arPar['DATA']){		foreach($arPar['DATA'] as $sKey => $sValue)
		{
			$sQuery .= urlencode($sKey).'='.urlencode($sValue).'&';
		}
	}

	if($arPar['DATA'] && ($arPar['METHOD'] == 'GET')){
		$arPar['URI'] .= '?'.$sQuery;
	}

	$sHead  = $arPar['METHOD']." ".$arPar['URI']." HTTP/1.0\r\n"
	."Host: ".$sHost."\r\n"
	."User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.6) Gecko/2009011913 MRA 5.3 (build 02557) Firefox/3.0.6\r\n"
	."Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
	."Accept-Language: ru,en-us;q=0.7,en;q=0.3\r\n"
	."Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7\r\n";

	if($arPar['DATA'] && ($arPar['METHOD'] == 'POST')){
        $sQuery = substr($sQuery, 0, -1);
        $sHead .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $sHead .= "Content-Length: ".strlen($sQuery)."\r\n\r\n";
        $sHead .= $sQuery;
    }

	$sHead .= "\r\n";

    if(!($rSocket = fsockopen($sHost, 80, $sCode, $sCode, 5))){
		return false;
	}
	fputs($rSocket, $sHead);
	while(!feof($rSocket))
	{
		$sCont .= fgets($rSocket, 128);
	}
	fclose($rSocket);
	return preg_replace("#.*?(\x0D\x0A){2}(.*)#sm", "$2", $sCont);
}

function urlQuery($arGet = array())
{
	$arURL = array();
	$sURL = $_GET['PATH'];
	if(count($_GET)>1 || $arGet){
		$sURL .= '?';
	}
	foreach($_GET as $sKey=>$sValue)
	{
		if($sKey == 'PATH' || isset($arGet[$sKey])){
			continue;
		}
		$arURL[] = $sKey.'='.urlencode($sValue);
	}
	foreach($arGet as $sKey=>$sValue)
	{
		$arURL[] = $sKey.'='.urlencode($sValue);
	}
	return $sURL.implode('&', $arURL);
}

function genPassword($nLength = 10)
{
	$sChar = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP';
	$sPassword = '';
	while($nLength--){
    	$sPassword .= $sChar[rand(0, (strlen($sChar)-1))];
	}
	return $sPassword;
}

function findTruth()
{
	foreach(func_get_args() as $sVal)
	{
		if($sVal){
			return $sVal;
		}
	}
}

function correctEmail($sEmail = '')
{
	return preg_match("#^([a-z0-9]+([_\-\.=+~][a-z0-9]){0,1})+@([a-z0-9]+([_\-\.][a-z0-9]){0,1})+\.[a-z]{2,4}$#i", $sEmail)?1:0;
}

function get_PosInt($sSimbol)
{
 	return abs((int)$sSimbol);
}

function get_FieldError($sField, $sMess = ' ', $sName = '')
{
    if(empty($_POST[$sField])) return str_replace('%NAME', $sName, $sMess);
    else
    if(!strlen(trim($_POST[$sField]))) return str_replace('%NAME', $sName, $sMess);

	return '';
}

function get_ErrorText($sMess = ' ', $sName = '')
{
	return str_replace('%NAME', $sName, $sMess);
}

function CorrectDate($sDate)
{
    $sDate = str_replace(array(' ', '.', '/'), array('', '-', '-'), $sDate);
	if(preg_match("#(\d{4})-(\d{2})-(\d{2})#", $sDate, $arMath)){
        if($arMath[1]>1995 && $arMath[1]<date('Y')+1){
        	if((int)$arMath[2]>0 && (int)$arMath[2]<13){
            	if((int)$arMath[3]>0 && (int)$arMath[3]<32){
                	return $sDate;
                }
            }
        }
    }
    return false;
}

function changeMessage($sName, $sKey = 'FORM_EMPTY_FIELD')
{
	global $MESS;
	if($MESS[$sKey]){
		$MESS[$sKey] = str_replace('%FIELD%', $sName, $MESS[$sKey]);
		return $MESS[$sKey];
	}
	return 'Неизвестная ошибка';
}

function html($sText)
{
	return htmlspecialchars($sText);
}

function htmlBack($sText)
{
	return str_replace(array(
		'&lt;', '&gt;', '&quot;', '&amp;', '&nbsp;'
	), array(
		'<', '>', '"', '&', ' '
	), $sText);
}

function Lang($sKey)
{
	global $MESS;
	if($MESS[$sKey]){
		return $MESS[$sKey];
	}
	return 'Неизвестная ошибка';
}

function Sandbox($sPath)
{
 	global $LIB, $MOD, $MOD_SETTING, $DB, $CURRENT, $USER;
 	include($_SERVER['DOCUMENT_ROOT'].$sPath);
}

function toArray($mText)
{
	return clearArray(explode(',', $mText));
}
?>