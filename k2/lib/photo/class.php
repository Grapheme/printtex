<?
class Photo
{
	function Resize($arPar = array())
    {
        if(!$arPar['WIDTH'] && !$arPar['HEIGHT']){
        	$this->Error = 'Задайте необходимые размеры';
        	return false;
        }
        $sFullPath = $_SERVER['DOCUMENT_ROOT'].$arPar['PATH'];
        if(!file_exists($sFullPath)){
        	$this->Error = 'Укажите верный путь';
        	return false;
        }
        if($arPar['MARK']){			$sFullPathMark = $_SERVER['DOCUMENT_ROOT'].$arPar['MARK'];
	        if(!file_exists($sFullPathMark)){
	        	$this->Error = 'Укажите верный путь к файлу WoterMark';
	        	return false;
	        }
        }
        $arPhotoProp = @getimagesize($sFullPath);
        if(!in_array($arPhotoProp['mime'], array('image/jpeg', 'image/gif', 'image/png'))){
        	$this->Error = 'Неправильный формат файла';
        	return false;
        }
        if($arPhotoProp[0] <= $arPhoto['WIDHT'] && $arPhotoProp[1] <= $arPhoto['HEIGHT']){
	    	return $arPar;
	    }

	    $arProp = array(
	    'PATH' => $sFullPath,
        'MIME' => $arPhotoProp['mime'],
        'REAL_WIDTH' => $arPhotoProp[0],
        'REAL_HEIGHT' => $arPhotoProp[1],
        'SET_WIDTH' => (int)$arPar['WIDTH'],
        'SET_HEIGHT' => (int)$arPar['HEIGHT'],
        'FIX' => $arPar['FIX'],
        'MARK' => $sFullPathMark,
        'QUALITY' => (empty($arPar['QUALITY'])?95:(int)$arPar['QUALITY'])
        );

        if(class_exists('imagick')){         	$ob = new PhotoIM;
        }else{
        	$ob = new PhotoGD;
        }
        if($arResize = $ob->Resize($arProp)){
	        return $arResize;
		}else{
			$this->Error = $ob->Error;
		}
        return false;
    }

    function Preview($nFile, $arPar)
    {    	global $LIB;

		if(!$arFile = $LIB['FILE']->ID($nFile)){
        	$this->Error = $LIB['FILE']->Error;
        	return false;
        }

		preg_match("#.+/(.+?)\.(.+?)$#i", $arFile['PATH'], $arMath);
	    $sFile = md5($arPar['WIDTH'].$arPar['HEIGHT'].$arPar['FIX'].$arPar['MARK'].$arMath[1]).'.'.strtolower($arMath[2]);
	    $sDir = '/files/preview/'.(int)$arPar['WIDTH'].'x'.(int)$arPar['HEIGHT'].'/';
	    $sFullPath = $_SERVER['DOCUMENT_ROOT'].$sDir.$sFile;

	    if(file_exists($sFullPath)){
	    	$arProp = getimagesize($sFullPath);
	    	return array('PATH' => $sDir.$sFile, 'WIDTH' => $arProp[0], 'HEIGHT' => $arProp[1]);
	    }else{
	    	if(file_exists($_SERVER['DOCUMENT_ROOT'].$arFile['PATH'])){
	    		@mkdir($_SERVER['DOCUMENT_ROOT'].$sDir, CHMOD_DIR);
	      		if(@copy($_SERVER['DOCUMENT_ROOT'].$arFile['PATH'], $sFullPath)){
	            	unset($this->Error, $this->Prop);
	            	if($arPhoto = $this->Resize(array(
	            	'PATH' => $sDir.$sFile,
	            	'WIDTH' => $arPar['WIDTH'],
	            	'HEIGHT' => $arPar['HEIGHT'],
	            	'FIX' => $arPar['FIX'],
	            	'MARK' => $arPar['MARK'],
	            	))){
	            		return array('PATH' => $sDir.$sFile, 'WIDTH' => $arPhoto['UPDATE_WIDTH'], 'HEIGHT' => $arPhoto['UPDATE_HEIGHT']);
	            	}
	      		}
	    	}
	    }
	    return false;
    }
}
?>
