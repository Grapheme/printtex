<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class MySQL
{
    var $DB;

	function MySQL()
	{
        return $this->Connect();
    }

    function Connect()
    {
        if(!$rConnect = @mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD)){        	if(!defined('MYSQL_HOST')){
            	if($_SERVER['REQUEST_URI'] != '/install/'){
            		header('Location: /install/');
            	}
            }            exit('Not connect to MySQL');
        }else{
            if(!mysql_select_db(MYSQL_DB, $rConnect)){            	exit('Not connect DB in MySQL');
            }else{
	            return $this->DB = $rConnect;
	        }
        }
    }

    function Query($sSQL)
    {
    	global $SETTING;

    	if($SETTING['DEBUG_PANEL']){
			$nTime = microtime(1);
			$rSQL = mysql_query($sSQL);
			$GLOBALS['DEBUG']['SQL_COUNT'] += 1;
			$GLOBALS['DEBUG']['SQL_TIME'] += round(microtime(1)-$nTime, 4);
			$GLOBALS['DEBUG']['SQL_QUERY'][] = $sSQL;
			$GLOBALS['DEBUG']['SQL_QUERY_TIME'][] = round(microtime(1)-$nTime, 4);
		}else{			$rSQL = mysql_query($sSQL);
		}

    	if(!mysql_error($this->DB)){
        	return $rSQL;
        }else{
        	$this->Error = mysql_error($this->DB);
        	return false;
        }
    }

    function Row($sSQL, $bCache = false)
    {
        if($bCache && defined('CACHE_MEMCACHE')){        	global $MOD;

			if($arData = $MOD['CACHE_MEMCACHE']->Get($sSQL)){
            	return $arData;
            }
            if(!$rResult = $this->Query($sSQL)){
    			return false;
    		}
    		$arData = mysql_fetch_assoc($rResult);
            $MOD['CACHE_MEMCACHE']->Set($sSQL, $arData, 200);
        	return $arData;
        }
    	if(!$rResult = $this->Query($sSQL)){
    		return false;
    	}
        return mysql_fetch_assoc($rResult);
    }

    function Rows($sSQL, $nSize = 0)
    {
        global $NAV_PAGE_TOTAL;

        $arRows = array();

        if($nSize){
            global $LIB;

        	$sSQL = str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS ', trim($sSQL));
        	if(substr($sSQL, -1, 1) == ';'){
        		$sSQL = substr($sSQL, 0, mb_strlen($sSQL, 'UTF-8')-1);
        	}

            $nPage = (int)$_GET['page'];
        	$nStart = 0;
        	if($nPage > 1){
        		$nStart = $nPage*$nSize-$nSize;
        	}
        	$sSQL_ = $sSQL.' LIMIT '.$nStart.', '.$nSize;

            if(!$rResult = $this->Query($sSQL_)){
	    		return false;
	    	}
	    	$nCount = mysql_num_rows($rResult);
	    	if(!$nCount){
            	$sSQL_ = $sSQL.' LIMIT 0, '.$nSize;
            	$rResult = $this->Query($sSQL_);
            	$nCount = mysql_num_rows($rResult);
	    	}

            for($i=0; $i<$nCount; $i++)
	    	{
	        	$arRows[] = mysql_fetch_assoc($rResult);
	        }
	        $arCount = $this->Row("SELECT FOUND_ROWS()");
	        $NAV_PAGE_TOTAL = $arCount['FOUND_ROWS()'];
            return $arRows;
        }

    	if(!$rResult = $this->Query($sSQL)){
    		return false;
    	}
    	for($i=0; $i<@mysql_num_rows($rResult); $i++)
    	{
        	$arRows[] = mysql_fetch_assoc($rResult);
        }
        return $arRows;
    }

    function Safe($sText)
    {
    	$sText = str_replace(array("\\", "'"), array("\\\\", "\'"), $sText);
    	return $sText;
    }

    function Insert($sSQL)
    {
        if($this->Query($sSQL)){
        	return mysql_insert_id($this->DB);
    	}
    	return false;
    }

    function CSQL($arPar)
    {
		global $LIB;

		$sRet = 'SELECT SQL_CALC_FOUND_ROWS';

		if($arPar['SELECT']){
			$bFirst = true;
			foreach($arPar['SELECT'] as $sValue)
			{
				if(!$bFirst){
					$sRet .= ', ';
				}
				$sVal = DBS($sValue);
				if($sVal == '*'){					$sRet .= ' *';
				}else{					$sRet .= ' `'.DBS($sValue).'`';
				}
				$bFirst = false;
			}
		}else{			$sRet .= ' *';
		}

		$sRet .= ' FROM '.$arPar['FROM'];

		if($arPar['WHERE']){
			$bFirst = true;
			$sRet .= ' WHERE';
			foreach($arPar['WHERE'] as $sKey => $sValue)
			{				if(!$bFirst){					$sRet .= ' AND';
				}

				if($sKey == '_SQL'){
					$sRet .= ' '.$sValue;
					break;
				}

				$sRet .= ' `'.DBS($sKey).'` = \''.DBS($sValue).'\'';
				$bFirst = false;
			}
		}

		if($arPar['ORDER_BY']){
			$bFirst = true;
			$sRet .= ' ORDER BY ';
			foreach($arPar['ORDER_BY'] as $sKey => $sValue)
			{
				if($sValue == 'RAND'){                	$sRet .= ' RAND()';
				}else{					if(!$bFirst){
						$sRet .= ' ,';
					}
					$sRet .= ' `'.DBS($sKey).'` '.DBS($sValue);
				}
				$bFirst = false;
			}
		}

		if($arPar['LIMIT']){
			if(is_array($arPar['LIMIT'])){				if(count($arPar['LIMIT'])>1){					$sRet .= ' LIMIT '.(int)$arPar['LIMIT'][0].', '.(int)$arPar['LIMIT'][1];
				}else{					$sRet .= ' LIMIT '.(int)$arPar['LIMIT'][0];
				}
			}else{				$sRet .= ' LIMIT '.(int)$arPar['LIMIT'];
			}
		}elseif($arPar['NAV']['SIZE']){
        	$nPage = (int)$_GET['page'];
        	$sRet .= ' LIMIT ';
        	$nStart = 0;
        	if($nPage > 1){
        		$nStart = $nPage*$arPar['NAV']['SIZE']-$arPar['NAV']['SIZE'];
        	}
        	$sRet .= $nStart.', '.$arPar['NAV']['SIZE'];
		}

		return $sRet;
    }

    function LastUpdate($arTable = array())
    {
    	global $DB;

    	if(!is_array($arTable)){
    		$arTable = array($arTable);
    	}
    	$sTime = '';
    	for($i=0; $i<count($arTable); $i++)
    	{
    		if($i){
    			$sTime .= ', ';
    		}

    		if($arRow = $DB->Row("CHECKSUM TABLE ".$arTable[$i]."")){
    			$sTime .= $arRow['Checksum'];
    		}
    	}

    	return $sTime;
    }

    function Dump($sFile)
	{
		if($sQuery = file_get_contents($sFile)){
			$sQuery = str_replace("\r", '', $sQuery);
			$arQuery = explode("\n", $sQuery);
			$i = 0;
			$this->Error = false;
			while ($i < count($arQuery)){
				$sQuery = trim($arQuery[$i]);
				if(mb_strlen($sQuery)){
					while(mb_substr($sQuery, mb_strlen($sQuery) - 1, 1) <> ';' && mb_substr($sQuery, 0, 1) <> '#' && mb_substr($sQuery, 0, 2) <> '--' && $i + 1 < count($arQuery)){
						$i++;
						$sQuery .= "\n".$arQuery[$i];
					}
					$sQuery = trim($sQuery);
					if(mb_substr($sQuery, 0, 1) <> '#' && mb_substr($sQuery, 0, 2) <> '--' && $sQuery != ''){
						@mysql_query($sQuery, $this->DB);
						if(mysql_error($this->DB)){
							$this->Error = mysql_error($this->DB);
						}
					}
				}
				$i++;
			}
			if($this->Error){				return false;
			}
		}
		return true;
	}
}

class DB extends MySQL
{

}
?>