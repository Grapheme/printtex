<?
class File
{
    function ID($nID)
	{
		global $DB, $LIB;
		if($arFile = $DB->Row("SELECT * FROM `k2_file` WHERE ID = '".(int)$nID."'")){
			$arFile['PATH'] = '/files/'.$arFile['PATH'];
			return $arFile;
		}
        $this->Error = 'Файл не найден';
        return false;
	}

	function Create($sPath, $sContent = '')
	{
		$sFullPath = $_SERVER['DOCUMENT_ROOT'].$sPath;
		$arExp = explode("/", $sPath);
		unset($arExp[count($arExp)-1]);
		$sDirPath = implode("/", $arExp);
		@mkdir($_SERVER['DOCUMENT_ROOT'].$sDirPath, CHMOD_DIR, true);
        if(substr($sPath, -1, 1) != '/'){
        	if(!$this->Edit($sPath, $sContent, 'w')){
	        	$this->Error = changeMessage($sFullPath, 'FILE_WRITE');
		        return false;
        	}
        	chmod($sFullPath, CHMOD_FILE);
        }else{
            if(file_exists($sFullPath)){
            	$this->Error = changeMessage($sFullPath, 'FILE_CREATED_DIR');
	            return false;
            }
        }
		return true;
	}

	function Add($sPath, $arPar)
	{
		global $DB, $LIB, $USER;
		if(!$arPar['FULL_PATH']){
			$sPath = $_SERVER['DOCUMENT_ROOT'].$sPath;
		}
		if(!file_exists($sPath)){
        	$this->Error = 'Укажите правильный путь к файлу';
			return false;
        }
 		if(strlen($arPar['PATH'])<1){
        	if($arPar['SECTION'] && $arPar['BLOCK']){
            	$arPath[] = 'section';
            	$arPath[] = $arPar['SECTION'];
            	$arPath[] = $arPar['BLOCK'];
        	}else{
        		$arPath[] = 'ophen';
        	}
        }else{
        	$arPath[] = $arPar['PATH'];
        }
        if(!preg_match("#(.+)\.(.+?)$#i", $arPar['NAME'], $arMath)){
        	return false;
        }

		if($arPar['TRANSLATION']){
			$sName = fileTranslation($arMath[1]);
			for($i=0; $i<10000; $i++)
			{
				if($i){
					$sSuffix = '('.$i.')';
				}
				if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/files/'.implode("/", $arPath).'/'.$sName.$sSuffix.'.'.$arMath[2])){
					$arPath[] = $sName.$sSuffix.'.'.$arMath[2];
					break;
				}
			}
		}else{
			$arPath[] = md5($arPar['NAME'].time()).'.'.$arMath[2];
		}

		$sFullPath = $_SERVER['DOCUMENT_ROOT'].'/files/'.implode("/", $arPath);
		if(copy($sPath, $sFullPath)){
        	chmod($sFullPath, CHMOD_FILE);
        	if($arPhotoProp = @getimagesize($sFullPath) && ($arPar['WIDTH'] || $arPar['HEIGHT'])){
            	$LIB['PHOTO']->Resize(array(
            	'PATH' => '/files/'.implode("/", $arPath),
            	'WIDTH' => $arPar['WIDTH'],
            	'HEIGHT' => $arPar['HEIGHT'],
            	'FIX' => $arPar['FIX']
            	));
        	}
        	clearstatcache();
        	$arFileProp = @getimagesize($sFullPath);
        	if($nID = $DB->Insert("INSERT INTO `k2_file` (`DATE_CREATED`, `USER`, `NAME`, `PATH`, `TYPE`, `SIZE`, `WIDTH`, `HEIGHT`, `DIR`) VALUES (
        	NOW(),
        	'".$USER['ID']."',
        	'".DBS($arPar['NAME'])."',
        	'".DBS(implode("/", $arPath))."',
        	'".DBS($arFileProp['mime'])."',
        	'".(int)filesize($sFullPath)."',
        	'".(int)$arFileProp[0]."',
        	'".(int)$arFileProp[1]."',
        	'".(int)$arPar['DIR']."')")){
            	return $nID;
        	}
		}
		return false;
	}

	function DeleteAll($arPar)
	{		global $DB, $LIB;

		$QB = new QueryBuilder;
	    $QB->From('k2_field')->Select('FIELD')->Where('`TYPE` = 4 AND `TABLE` = ?', $arPar['TABLE']);
	    if($arPar['FIELD']){	    	$QB->Where('ID = ?', $arPar['FIELD']);
	    }

		$arField = $DB->Rows($QB->Build());
		$QB = new QueryBuilder;
		$QB->From($arPar['TABLE']);

		if($arPar['ELEMENT']){
	    	$QB->Where('ID = ?', $arPar['ELEMENT']);
	    }
		for($i=0; $i<count($arField); $i++)
		{			$QB->Select($arField[$i]['FIELD']);
		}

		$arElement = $DB->Rows($QB->Build());
        for($i=0; $i<count($arElement); $i++)
		{
			for($j=0; $j<count($arField); $j++)
			{
				$this->Delete($arElement[$i][$arField[$j]['FIELD']]);
			}
		}
	}

	function Delete($mID)
	{		global $DB, $LIB;

		$arList = clearArray(explode(',', $mID));
		for($i=0; $i<count($arList); $i++)
		{
			if($arFile = $this->ID($arList[$i])){
	        	unlink($_SERVER['DOCUMENT_ROOT'].'/'.$arFile['PATH']);
				$DB->Query("DELETE FROM `k2_file` WHERE `ID` = '".$arFile['ID']."'");
	        }
		}
	}

    function Upload($arPar)
    {
		if(!is_uploaded_file($arPar['tmp_name']) || $arPar['error']){			$this->Error = 'Файл не загружен';
			return false;
		}
		$arPar['FULL_PATH'] = 1;
		$arPar['NAME'] = $arPar['name'];
        if($nID = $this->Add($arPar['tmp_name'], $arPar)){        	return $nID;
        }
		return false;
    }

	function Read($sPath)
	{
    	$sFullPath = $_SERVER['DOCUMENT_ROOT'].$sPath;
    	if($sCont = @file_get_contents($sFullPath)){
    		return $sCont;
    	}else{
        	$arAnalytic = $this->Analytic($sFullPath);
        	if(!$arAnalytic['EXISTS']){
            	$this->Error = changeMessage($sFullPath, 'FILE_EXIST');
            	return false;
        	}elseif(!$arAnalytic['READABLE']){
        		$this->Error = changeMessage($sFullPath, 'FILE_READABLE');
            	return false;
        	}
    	}
	}

	function Edit($sPath, $sContent = '', $sKey = 'w')
	{
    	$sFullPath = $_SERVER['DOCUMENT_ROOT'].$sPath;
    	$rFile = @fopen($sFullPath, $sKey);
    	if(@fwrite($rFile, $sContent) !== false){
    		return true;
    	}else{
    		$arAnalytic = $this->Analytic($sFullPath);
    		if(!$arAnalytic['EXISTS']){
            	$this->Error = changeMessage($sFullPath, 'FILE_EXISTS');
            	return false;
        	}elseif(!$arAnalytic['WRITABLE']){
        		$this->Error = changeMessage($sFullPath, 'FILE_WRITABLE');
            	return false;
        	}
        	return false;
    	}
	}

	function Analytic($sFullPath)
	{
 		return array(
 		'EXISTS' => file_exists($sFullPath),
 		'READABLE' => is_readable($sFullPath),
 		'WRITABLE' => is_writable($sFullPath),
 		);
	}

	function Check($arFile, $arPar)
	{    	if(!$arFile['name'] || $arFile['error']){
    		return 'Загрузите файл в поле &laquo;'.$arPar['FIELD_NAME'].'&raquo;';
    	}

    	if($arPar['TYPE']){
	        preg_match("#.+\.(.+?)$#i", $arFile['name'], $arMath);
	        $sExt = strtolower($arMath[1]);

	        if($arPar['TYPE'] == 'IMAGE'){
				$arExt = array('jpg', 'jpeg', 'gif', 'png');
				if(!in_array($sExt, $arExt) || !preg_match("#^image/#", $arFile['type'])){
					return 'В поле &laquo;'.$arPar['FIELD_NAME'].'&raquo; неверный тип файла';
	            }
	    	}
    	}
	}

	function IsPhoto($nID)
	{		if(!$arFile = $this->ID($nID)){
        	return false;
        }
        preg_match("#.+\.(.+?)$#i", $arFile['NAME'], $arMath);
		if(!in_array(strtolower($arMath[1]), array('jpg', 'jpeg', 'gif', 'png')) || !preg_match("#^image/#", $arFile['TYPE'])){			return false;
		}
		return true;
	}
}
?>