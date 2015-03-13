<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class Currency
{
    function Show($sTemplate = 'default')
	{
    	global $DB, $LIB, $MOD, $MOD_SETTING, $CURRENT, $USER;

    	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/currency/template/'.$sTemplate.'/controller/index.php');
	}

    function Rows()
    {    	global $DB, $LIB;

        return $DB->Rows("SELECT * FROM `k2_mod_currency`");
    }

    function Update()
    {    	global $DB, $LIB, $MOD_SETTING;

    	if($sXML = $this->Content()){	    	$oXML = simplexml_load_string($sXML);
			$bCount = false;
            $sDate = (string)$oXML->attributes();
			foreach($oXML->Valute as $oValute){
				if(trim($oValute->CharCode) && trim($oValute->Name) && trim($oValute->Value)){
					$sCode = DBS($oValute->CharCode);
					$arList[$sCode] = array(
					'NOMINAL' => $oValute->Nominal,
					'NAME' => $oValute->Name,
					'VALUE' => str_replace(',', '.', (string)$oValute->Value),
					'DATE' => $sDate
					);
					if($DB->Query("
					REPLACE INTO `k2_mod_currency` (
						`CODE`,
						`NOMINAL`,
						`NAME`,
						`VALUE`,
						`DATE`
					)VALUES(
						'".DBS($sCode)."',
						'".DBS($arList[$sCode]['NOMINAL'])."',
						'".DBS($arList[$sCode]['NAME'])."',
						'".DBS($arList[$sCode]['VALUE'])."',
						'".$sDate."'
					)")){                   		$bCount = true;
					}
				}
			}
			if($bCount){				return true;
			}
    	}

		return false;
    }

    function Content()
	{
		$sXML = '';
		if(!($rSocket = fsockopen ('www.cbr.ru', 80, $sErrorCode, $sErrorText, 5))){
			$this->Error = 'Не удалось установить соединение';
			return false;
		}
		fputs($rSocket, "GET /scripts/XML_daily.asp HTTP/1.0\r\nHost: cbr.ru\r\n\r\n");
		while (!feof($rSocket)){
			$sXML .= fgets ($rSocket, 128);
		}
		if(empty($sXML)){
			$this->Error = 'Не удалось загрузить файл с сервера www.cbr.ru';
			return false;
		}
		fclose($rSocket);
		return preg_replace("#.*<\?#s", "<?", $sXML);
	}
}
?>