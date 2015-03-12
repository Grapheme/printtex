<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class Weather
{	function Show($sTemplate = 'default')
	{
    	global $DB, $LIB, $MOD, $MOD_SETTING, $CURRENT, $USER;

    	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/weather/template/'.$sTemplate.'/controller/index.php');
	}

    function Rows($nCode = 0)
    {
    	global $DB, $LIB, $MOD_SETTING;

    	if(!$nCode){    		$nCode = $MOD_SETTING['WEATHER']['CODE'];
    	}

        return $this->Update($nCode);
    }

    function Update($nCode = 0)
    {
    	global $DB, $LIB, $MOD_SETTING;

		if(!$nCode){
    		$nCode = $MOD_SETTING['WEATHER']['CODE'];
    	}

    	if(!$sXml = $this->Content($nCode)){
    		return false;
    	}

    	$ob = simplexml_load_string($sXml);
		foreach($ob->REPORT->TOWN->FORECAST as $obForecast)
		{
  			$arTmpList = array();
  			foreach($obForecast->attributes() as $key=>$value)
  			{
                $arTmpList['FORECAST'][$key] = (string)$value;
  			}
			foreach($obForecast as $key=>$obType)
  			{
                foreach($obType->attributes() as $key_=>$value)
	  			{
	                $arTmpList[$key][$key_] = (string)$value;
	  			}
  			}
  			$arList[] = $arTmpList;
		}
		if(!$arList){			$this->Error = 'Получены некорректные данные с сервера informer.gismeteo.ru';
			return false;
		}

		return $arList;
    }

    function Content($nCode)
	{
		if(!$sContent = httpRequest('informer.gismeteo.ru', array('URI' => '/xml/'.$nCode.'_1.xml'))){			$this->Error = 'Не удалось загрузить файл с сервера informer.gismeteo.ru';
			return false;
		}
		return $sContent;
	}

	function Sign($nNum)
	{		if($nNum > 0){			return '+'.$nNum;
		}
		return $nNum;
	}
}
?>