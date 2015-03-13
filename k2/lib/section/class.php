<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class Section
{
	function ID($nID, $bFullInfo = 0)
	{
		global $LIB, $DB;
		if($arSection = $DB->Rows("SELECT * FROM `k2_section` WHERE `ID` = '".$nID."'")){
			if($bFullInfo){
				$arSectionBlock = $LIB['SECTION_BLOCK']->Rows($nID);
				for($i=0; $i<count($arSectionBlock); $i++)
				{
					unset($arSectionBlock[$i]['SECTION']);
					$arSection[0]['BLOCK'][] = $arSectionBlock[$i];
				}
			}
			return $arSection[0];
        }
        $this->Error = 'Раздел не найден';
		return false;
	}

	function Rows($nSite)
	{
    	global $DB;
    	$arRows = $DB->Rows("SELECT * FROM `k2_section` WHERE `SITE` = '".$nSite."' ORDER BY `SORT` ASC");
		return $arRows;
	}

	function Correct($arPar, $arSection = array())
	{
        global $DB, $LIB;
        $arPar += $arSection;
    	if(isset($arPar['NAME']) && !strlen($arPar['NAME'])){
			$this->Error = changeMessage('Название');
			return false;
		}
		if(isset($arPar['FOLDER'])){
			if(!strlen($arPar['FOLDER'])){
				$this->Error = changeMessage('Папка');
				return false;
			}
            if(!preg_match("#^[a-z0-9\-_]+$#", $arPar['FOLDER'])){
				$this->Error = 'В названии папки допускаются латинские буквы, цифры, тире и нижнее подчеркивание';
				return false;
			}
		}
		$arPar['URL'] = '/'.$arPar['FOLDER'].'/';
		if($arPar['ID']){
	        $arBackPath = $LIB['SECTION']->Back(array('ID' => $arPar['ID']));
			$arPar['URL'] = '/'.$arPar['FOLDER'].'/';
	        if($arBackPath[count($arBackPath)-2]['URL']){
	        	$arPar['URL'] = $arBackPath[count($arBackPath)-2]['URL'].$arPar['FOLDER'].'/';
	        }
    	}else{
			if($arPar['PARENT']){
				if($arParentSection = $this->ID($arPar['PARENT'])){
					$arPar['URL'] = $arParentSection['URL'].$arPar['FOLDER'].'/';
				}
			}
    	}
    	if($DB->Rows("SELECT * FROM `k2_section` WHERE `SITE` = '".(int)$arPar['SITE']."' AND `URL` = '".DBS($arPar['URL'])."'".($arPar['ID']?" AND ID != '".$arPar['ID']."'":""))){
			$this->Error = 'Раздел с такой папкой уже существует';
			return false;
		}
    	return $arPar;
	}

	function Add($nSite, $arPar = array())
	{
		global $LIB, $DB, $USER;
		if(!$arSite = $LIB['SITE']->ID($nSite)){
        	return false;
        }

		if(empty($arPar['NAME'])){
			$this->Error = changeMessage('Название');
			return false;
		}
		if(empty($arPar['FOLDER'])){
			$this->Error = changeMessage('Папка');
			return false;
		}
		$arPar['FOLDER'] = strtolower(trim($arPar['FOLDER']));
		if(!preg_match("#^[a-z0-9\-_]+$#", $arPar['FOLDER'])){
			$this->Error = 'В названии папки допускаются латинские буквы, цифры, тире и нижнее подчеркивание';
			return false;
		}
		$sFullPath = '/'.$arPar['FOLDER'].'/';

		if($arPar['PARENT']){
			if($arParentSection = $this->ID($arPar['PARENT'])){
				$arPar['PERMISSION'] = $arParentSection['PERMISSION'];
				$arPar['DESIGN_SHOW'] = $arParentSection['DESIGN_SHOW'];
				$sFullPath = $arParentSection['URL'].$arPar['FOLDER'].'/';
			}
		}elseif(!$arPar['DESIGN']){			$arPar['DESIGN_SHOW'] = $arSite['DESIGN'];
		}
		if($DB->Rows("SELECT `ID` FROM `k2_section` WHERE `SITE` = '".(int)$nSite."' AND `URL` = '".DBS($sFullPath)."'")){
			$this->Error = 'Такая папка уже существует';
			return false;
		}

		if($sError = $LIB['FIELD']->CheckAll('k2_section', $arPar)){
       		$this->Error = $sError;
			return false;
        }

		$nLevel = count(explode('/', $sFullPath))-3;
		$nSort = 10;
		if($arSection = $DB->Rows("SELECT `SORT` FROM `k2_section` WHERE `PARENT` = '".(int)$arPar['PARENT']."' ORDER BY `SORT` DESC LIMIT 1")){
			$nSort = $arSection[0]['SORT']+10;
		}
		if($nID = $DB->Insert("
		INSERT INTO `k2_section`(
			`ACTIVE`,
			`NAME`,
			`SORT`,
			`SITE`,
			`PARENT`,
			`FOLDER`,
			`EXTERNAL`,
			`URL`,
			`LEVEL`,
			`DESIGN`,
			`DESIGN_SHOW`,
			`PERMISSION`
		)VALUES(
			'".(int)$arPar['ACTIVE']."', '".DBS($arPar['NAME'])."', '".(int)$nSort."', '".$nSite."', '".(int)$arPar['PARENT']."', '".DBS($arPar['FOLDER'])."', '".DBS($arPar['EXTERNAL'])."', '".DBS($sFullPath)."', '".$nLevel."', '".(int)$arPar['DESIGN']."', '".(int)$arPar['DESIGN_SHOW']."', '".(int)$arPar['PERMISSION']."'
		)")){

			if($USER['USER_GROUP'] != 1){				$arPermission = $USER['PERMISSION']['SECTION'];
				if($USER['PERMISSION']['SECTION'][$arPar['PARENT']]){					$arPermission[$nID] = $USER['PERMISSION']['SECTION'][$arPar['PARENT']];
				}
				$LIB['USER_GROUP']->Edit($USER['USER_GROUP'], array('PERMISSION_SECTION' => $arPermission));
			}
			mkdir($_SERVER['DOCUMENT_ROOT'].'/files/section/'.$nID);
			mkdir($_SERVER['DOCUMENT_ROOT'].'/k2/admin/files/section/'.$nID);

			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_section', 'PATH' => 'section/'.$nID), $arPar);
        	return $nID;
		}

    	return false;
	}

	function Edit($nID, $arPar = array(), $bFull = 0)
	{
		global $LIB, $DB, $USER;

        if(!$arSection = $this->ID($nID)){
        	return false;
        }
        if(!$arSite = $LIB['SITE']->ID($arSection['SITE'])){
        	return false;
        }

        if(!$bFull){
        	$arPar += $arSection;
        }

        if(!$arPar = $this->Correct($arPar, $arSection)){
        	return false;
        }

        if($sError = $LIB['FIELD']->CheckAll('k2_section', $arPar)){
       		$this->Error = $sError;
			return false;
        }

        if($arPar['PARENT'] && $arParentSection = $this->ID($arPar['PARENT'])){
        	$arPar['DESIGN_SHOW'] = $arParentSection['DESIGN_SHOW'];
        }else{
			$arSite = $LIB['SITE']->ID($arSection['SITE']);
			$arPar['DESIGN_SHOW'] = $arSite['DESIGN'];
		}

		if($arPar['PARENT']){
			$sFullPath = '/'.$arPar['FOLDER'].'/';
			if($arParentSection = $this->ID($arPar['PARENT'])){
				$sFullPath = $arParentSection['URL'].$arPar['FOLDER'].'/';
			}
			$arPar['URL'] = $sFullPath;
			$arPar['LEVEL'] = count(explode('/', $sFullPath))-3;
			if($DB->Rows("SELECT `ID` FROM `k2_section` WHERE `ID` != '".$nID."' AND `SITE` = '".$arPar['SITE']."' AND `URL` = '".DBS($sFullPath)."'")){
				$this->Error = 'Такая папка уже существует';
				return false;
			}
		}

        if($arPar['DESIGN']){        	$arPar['DESIGN_SHOW'] = $arPar['DESIGN'];
        }

        if($DB->Query("UPDATE
        	`k2_section`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`NAME` = '".DBS($arPar['NAME'])."',
			`SORT` = '".(int)$arPar['SORT']."',
			`PARENT` = '".(int)$arPar['PARENT']."',
			`FOLDER` = '".DBS($arPar['FOLDER'])."',
			`EXTERNAL` = '".DBS($arPar['EXTERNAL'])."',
			`URL` = '".DBS($arPar['URL'])."',
			`LEVEL` = '".(int)$arPar['LEVEL']."',
			`DESIGN` = '".(int)$arPar['DESIGN']."',
			`DESIGN_SHOW` = '".(int)$arPar['DESIGN_SHOW']."',
			`PERMISSION` = '".(int)$arPar['PERMISSION']."'
        WHERE
        	ID = '".$nID."';
        ")){
    		$arList = $this->Child(array('ID' => $nID));
    		for($i=0; $i<count($arList); $i++)
    		{
    		 	$this->Edit($arList[$i]['ID'], $arList[$i]);
    		}
        	$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_section', 'PATH' => 'section/'.$nID), $arPar);
	        return $nID;
        }

    	return false;
	}

	function Delete($nID)
	{
    	global $LIB, $DB;

    	if(!$arSection = $this->ID($nID)){
        	return false;
        }

    	$arList = $this->Child(array('ID' => $nID));

    	$arList[] = array('ID' => $nID);
    	for($i=0; $i<count($arList); $i++)
    	{
    		$arSBlock = $LIB['SECTION_BLOCK']->Rows($arList[$i]['ID']);
            for($n=0; $n<count($arSBlock); $n++)
            {            	$LIB['SECTION_BLOCK']->Delete($arSBlock[$n]['ID']);
            }
            $LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_section', 'ELEMENT' => $arList[$i]['ID']));
    		$DB->Query("DELETE FROM `k2_section` WHERE `ID` = '".$arList[$i]['ID']."'");
    		rmdir($_SERVER['DOCUMENT_ROOT'].'/files/section/'.$nID);
    	}
		return true;
	}

	function Back($arPar, $arList = array())
	{
		global $DB, $LIB;
	    if($arCategory = $DB->Rows("SELECT * FROM `k2_section` WHERE `ID` = '".(int)$arPar['ID']."'".($arPar['ACTIVE']?" AND `ACTIVE` = 1":""))){
	    	$arList[] = $arCategory[0];
	    	if($arCategory[0]['PARENT']){
	    		$arPar['ID'] = $arCategory[0]['PARENT'];
	    		$arList = $this->Back($arPar, $arList);
	    	}else{
	    		$arList = array_reverse($arList);
	    	}
	    }
	    return $arList;
	}

	function Child($arPar, $arList = array())
	{
		global $DB, $LIB;

	    if($arSection = $DB->Rows("SELECT * FROM `k2_section` WHERE `PARENT` = '".(int)$arPar['ID']."'".($arPar['ACTIVE']?" AND `ACTIVE` = 1":"")." ORDER BY `SORT` ASC")){
	    	for($i=0; $i<count($arSection); $i++)
	    	{
	    		$arList[] = $arSection[$i];
	    		if($arPar['RECURSIVE']){
		    		$arPar['ID'] = $arSection[$i]['ID'];
		    		$arList = $this->Child($arPar, $arList);
	    		}
	    	}
	    }

	    return $arList;
	}

	function Map($arPar, $arList = array(), $nLevel = 1)
	{
		global $DB;
		$arRows = $DB->Rows("SELECT * FROM `k2_section` WHERE `SITE` = '".(int)$arPar['SITE']."' AND `PARENT` = '".(int)$arPar['ID']."'".($arPar['ACTIVE']?" AND `ACTIVE` = 1":"")." ORDER BY `SORT` ASC");
		for($i=0; $i<count($arRows); $i++)
		{
			$arRows[$i]['LEVEL'] = $nLevel;
			$arList[] = $arRows[$i];
			$arPar['ID'] = $arRows[$i]['ID'];
			$arList = $this->Map($arPar, $arList, $nLevel+1);
		}
		return $arList;
	}
}
?>