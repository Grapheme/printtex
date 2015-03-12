<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class Component
{
	function ID($nID, $bField = false, $bTemplate = false)
	{
		global $LIB, $DB;

		if($arComponent = $DB->Row("SELECT * FROM `k2_component` WHERE `ID` = '".$nID."'")){
        	if($bField){
	        	$arComponent['ELEMENT_FIELD'] = $LIB['FIELD']->Rows('k2_component'.$nID);
				$arComponent['CATEGORY_FIELD'] = $LIB['FIELD']->Rows('k2_component'.$nID.'category');
        	}
        	if($bTemplate){
	        	$arComponent['TEMPLATE'] = $LIB['FILE']->Read('/k2/dev/component/'.$nID.'/template.php');
        	}
			return $arComponent;
        }

        $this->Error = 'Компонент не найден';
		return false;
	}

	function Rows($nGroup = false)
	{
    	global $DB;
    	if($nGroup){
    		$arRows = $DB->Rows("SELECT * FROM `k2_component` WHERE `COMPONENT_GROUP` = '".(int)$nGroup."' ORDER BY `ID` ASC");
    	}else{
    		$arRows = $DB->Rows("SELECT * FROM `k2_component` ORDER BY `ID` ASC");
    	}
		return $arRows;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB, $USER;

		if($sError = formCheck(array('NAME' => 'Название', 'COMPONENT_GROUP' => 'Группа'), $arPar)){
       		$this->Error = $sError;
			return false;
        }

		if(!$arPar['ICON']){
        	$arPar['ICON'] = 'default';
		}

		if($nID = $DB->Insert("
		INSERT INTO `k2_component` (
			`NAME`,
			`COMPONENT_GROUP`,
			`CATEGORY`,
			`ICON`
		)VALUES(
			'".DBS($arPar['NAME'])."', '".(int)$arPar['COMPONENT_GROUP']."', '".DBS($arPar['CATEGORY'])."', '".DBS($arPar['ICON'])."'
		);
		")){
			if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/files/component/'.$nID, CHMOD_DIR)){
				$this->Error = 'Не удается создать папку '.$_SERVER['DOCUMENT_ROOT'].'/files/component/'.$nID;
				return false;
			}
			if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/k2/dev/component/'.$nID, CHMOD_DIR)){
				$this->Error = 'Не удается создать папку '.$_SERVER['DOCUMENT_ROOT'].'/k2/dev/component/'.$nID;
				return false;
			}
			if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/k2/dev/component/'.$nID.'/static/', CHMOD_DIR)){
				$this->Error = 'Не удается создать папку '.$_SERVER['DOCUMENT_ROOT'].'/k2/dev/component/'.$nID.'/static/';
				return false;
			}
			$arExs[] = $LIB['FILE']->Create('/k2/dev/component/'.$nID.'/static/.htaccess', 'allow from all');
			$arExs[] = $LIB['FILE']->Create('/k2/dev/component/'.$nID.'/template.php', $arPar['TEMPLATE']);

			if(in_array('', $arExs)){
				$DB->Query("DELETE FROM `k2_component` WHERE ID = '".$nID."'");
				$this->Error = $LIB['FILE']->Error;
			}else{
            	if($DB->Query("CREATE TABLE `k2_component".$nID."` (
					`ID` int(11) NOT NULL AUTO_INCREMENT,
					`DATE_CREATED` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					`DATE_CHANGE` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					`USER_CREATED` int(11) NOT NULL,
					`USER_CHANGE` int(11) NOT NULL,
					`ACTIVE` tinyint(1) NOT NULL DEFAULT '0',
					`SORT` int(11) NOT NULL,
					`COLLECTION` int(11) NOT NULL,
					`CATEGORY` int(11) NOT NULL,
					PRIMARY KEY (`ID`)
				)ENGINE=MyISAM DEFAULT CHARSET=utf8;") &&
				$DB->Query("CREATE TABLE `k2_component".$nID."category` (
					`ID` int(11) NOT NULL auto_increment,
					`DATE_CREATED` datetime NOT NULL default '0000-00-00 00:00:00',
					`DATE_CHANGE` datetime NOT NULL default '0000-00-00 00:00:00',
					`USER_CREATED` int(11) NOT NULL,
					`USER_CHANGE` int(11) NOT NULL,
					`COLLECTION` int(11) NOT NULL,
					`PARENT` int(11) NOT NULL,
					`ACTIVE` tinyint(1) NOT NULL,
					`SORT` int(11) NOT NULL,
					`NAME` varchar(255) NOT NULL,
					PRIMARY KEY  (`ID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8")){
					$this->CreateIcon(array(
	            	'ID' => $nID,
	            	'NAME' => $arPar['NAME'],
	            	'ICON' => $arPar['ICON']
	            	));
					return $nID;
				}
			}
		}
    	return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB, $USER;

        if(!$arComponent = $this->ID($nID)){
        	return false;
        }

        if($sError = formCheck(array('NAME' => 'Название', 'COMPONENT_GROUP' => 'Группа'))){
       		$this->Error = $sError;
			return false;
        }

        if($DB->Query("UPDATE k2_component
        SET
			`NAME` = '".DBS($arPar['NAME'])."',
			`COMPONENT_GROUP` = '".(int)$arPar['COMPONENT_GROUP']."',
			`CATEGORY` = '".(int)$arPar['CATEGORY']."',
			`ICON` = '".DBS($arPar['ICON'])."'
        WHERE
        	`ID` = '".$nID."';
        ")){
	        $arExs[] = $LIB['FILE']->Edit('/k2/dev/component/'.$nID.'/template.php', $arPar['TEMPLATE']);
	        if(in_array('', $arExs)){
				$this->Error = $LIB['FILE']->Error;
				return false;
			}else{
            	if($arComponent['NAME'] != $arPar['NAME'] || $arComponent['ICON'] != $arPar['ICON']){            		$this->CreateIcon(array(
            		'ID' => $nID,
            		'NAME' => $arPar['NAME'],
            		'ICON' => $arPar['ICON']
            		));
            	}
            	return $nID;
			}
        }
    	return false;
	}

	function Delete($nID)
	{
    	global $LIB, $DB;

    	if(!$arComponent = $this->ID($nID)){
        	return false;
        }

	    $LIB['FILE']->DeleteAll(array('TABLE' => 'k2_component'.$arComponent['ID']));
        $LIB['FILE']->DeleteAll(array('TABLE' => 'k2_component'.$arComponent['ID'].'category'));

	    $DB->Query("DELETE FROM `k2_component` WHERE `ID` = '".$arComponent['ID']."'");
	    $DB->Query("DROP TABLE `k2_component".$arComponent['ID']."`");
	    $DB->Query("DROP TABLE `k2_component".$arComponent['ID']."category`");
	    $DB->Query("DELETE FROM `k2_field` WHERE `TABLE` = 'k2_component".$arComponent['ID']."' OR `TABLE` = 'k2_component".$arComponent['ID']."category'");
        dirDelete($_SERVER['DOCUMENT_ROOT'].'/k2/dev/component/'.$arComponent['ID']);
        dirDelete($_SERVER['DOCUMENT_ROOT'].'/files/component/'.$arComponent['ID']);
		return true;
	}

	function Export($nID)
	{
    	global $LIB, $SYSTEM;

    	if(!$arComponent = $this->ID($nID, true)){
			return false;
		}
		$arExport['VERSION'] = $SYSTEM['VERSION'];
		$arExport['VERSION_KEY'] = $SYSTEM['VERSION_KEY'];
		$arExport['COMPONENT'] = $arComponent;
		$sContent = serialize($arExport);

		$sPath = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/component/'.$arComponent['ID'].'/';
		$sZipFile = $_SERVER['DOCUMENT_ROOT'].'/tmp/k2component'.md5(microtime()).'.zip';

		@unlink($sZipFile);
        $arFile = dirList($sPath);
		$zip = new ZipArchive;
		if($zip->open($sZipFile, ZIPARCHIVE::CREATE) !== true){
			$this->Error = 'Не удалось экспортировать компонент';
			return false;
		}
		$zip->addEmptyDir('file');
		for($i=0; $i<count($arFile); $i++)
		{
	    	if(is_dir($sPath.$arFile[$i])){
	    		$zip->addEmptyDir('file/'.$arFile[$i]);
	    	}else{
	        	$zip->addFile($sPath.$arFile[$i], 'file/'.$arFile[$i]);
	    	}
		}
		$zip->addFromString('component.php', $sContent);
		$zip->close();

		return $sZipFile;
	}

	function Import($sPar)
	{
    	global $LIB, $SYSTEM;

        if($sError = formCheck(array('COMPONENT_GROUP' => 'Группа'), $sPar)){
       		$this->Error = $sError;
			return false;
        }
        if(($sPar['FILE']['type'] != 'application/zip') || !file_exists($sPar['FILE']['tmp_name'])){
       		$this->Error = 'Загрузите файл';
			return false;
        }
        $sDir = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5(microtime()).'/';
        if(!mkdir($sDir, CHMOD_DIR)){
        	$this->Error = 'Не удалось создать временную папку '.$sDir;
			return false;
        }
        if(!unZip($sPar['FILE']['tmp_name'], $sDir)){
        	$this->Error = 'Не удается распаковать архив';
			dirDelete($sDir);
			return false;
        }
        $arComponent = unserialize(file_get_contents($sDir.'component.php'));
        if($SYSTEM['VERSION_KEY'] != $arComponent['VERSION_KEY']){
        	$this->Error = 'Файл импорта не поддерживается этой версией системы';
			dirDelete($sDir);
			return false;
        }
        $arComponent['COMPONENT']['COMPONENT_GROUP'] = $sPar['COMPONENT_GROUP'];
    	if(!($nComponent = $LIB['COMPONENT']->Add($arComponent['COMPONENT']))){
    		$this->Error = 'Не удалось импортировать компонент';
			dirDelete($sDir);
			return false;
    	}
       	for($i=0; $i<count($arComponent['COMPONENT']['ELEMENT_FIELD']); $i++)
       	{
       		$arComponent['COMPONENT']['ELEMENT_FIELD'][$i]['SETTING'] = unserialize($arComponent['COMPONENT']['ELEMENT_FIELD'][$i]['SETTING']);
       		unset($arComponent['COMPONENT']['ELEMENT_FIELD'][$i]['TABLE']);
       		if(!$LIB['FIELD']->Add('k2_component'.$nComponent, $arComponent['COMPONENT']['ELEMENT_FIELD'][$i])){
       			$bError = true;
       		}
       	}
       	for($i=0; $i<count($arComponent['COMPONENT']['CATEGORY_FIELD']); $i++)
       	{
       		$arComponent['COMPONENT']['CATEGORY_FIELD'][$i]['SETTING'] = unserialize($arComponent['COMPONENT']['CATEGORY_FIELD'][$i]['SETTING']);
       		unset($arComponent['COMPONENT']['CATEGORY_FIELD'][$i]['TABLE']);
       		if(!$LIB['FIELD']->Add('k2_component'.$nComponent.'category', $arComponent['COMPONENT']['CATEGORY_FIELD'][$i])){
       			$bError = true;
       		}
       	}
       	if($bError){
    		$this->Delete($nComponent);
    		$this->Error = 'Не удалось обработать файл';
    		dirDelete($sDir);
    		return false;
    	}
        dirCopy($sDir.'file/', $_SERVER['DOCUMENT_ROOT'].'/k2/dev/component/'.$nComponent.'/');
        dirDelete($sDir);

        return $nComponent;
	}

	function DeleteUnused($nField)
	{    	global $LIB, $DB;

    	if(!$arField = $LIB['FIELD']->ID($nField)){
        	$this->Error = $LIB['FIELD']->Error;
        	return false;
        }
        $arBlock = $DB->Rows("SELECT `".$arField['FIELD']."` AS `TEXT` FROM `".$arField['TABLE']."` WHERE `".$arField['FIELD']."` != ''");
        for($i=0; $i<count($arBlock); $i++)
        {        	preg_match_all("#<!-- component(\d+):(\d+) -->#", $arBlock[$i]['TEXT'], $arMath);
        	for($j=0; $j<count($arMath[1]); $j++)
            {            	$arFind[$arMath[1][$j]][] = $arMath[2][$j];
            }
        }
        $arCField = $DB->Rows("SELECT `COMPONENT`, `COLLECTION` FROM `k2_component_field` WHERE `FIELD` = '".$arField['ID']."'");
        for($i=0; $i<count($arCField); $i++)
        {
        	if(!$arFind[$arCField[$i]['COMPONENT']] || !in_array($arCField[$i]['COLLECTION'], $arFind[$arCField[$i]['COMPONENT']])){
       			if($arContent = $LIB['COMPONENT_CATEGORY']->Rows($arCField[$i]['COMPONENT'], array('COLLECTION' => $arCField[$i]['COLLECTION'], 'PARENT' => 0), false, array('ID'))){
               		$LIB['COMPONENT_CATEGORY']->Delete($arContent[0]['ID'], $arCField[$i]['COMPONENT']);
               	}
               	if($arContent = $LIB['COMPONENT_ELEMENT']->Rows($arCField[$i]['COMPONENT'], array('COLLECTION' => $arCField[$i]['COLLECTION']), false, array('ID'))){
               		for($j=0; $j<count($arContent); $j++)
               		{
               			$LIB['COMPONENT_ELEMENT']->Delete($arContent[$j]['ID'], $arCField[$i]['COMPONENT']);
               		}
               	}
               	$DB->Query("DELETE FROM `k2_component_field` WHERE `COMPONENT` = '".$arCField[$i]['COMPONENT']."' AND `FIELD` = '".$nField."' AND `COLLECTION` = '".$arCField[$i]['COLLECTION']."'");
            }
        }
	}

	function FindAndDelete($arPar)
	{
		global $DB, $LIB;

		preg_match_all("#<!-- component(\d+):(\d+) -->#", $arPar['TEXT'], $arMath);
       	for($i=0; $i<count($arMath[1]); $i++)
       	{       		$nComponent = $arMath[1][$i];
           	$nCollection = $arMath[2][$i];

           	$arElement = $DB->Rows("SELECT ID FROM `k2_component".$nComponent."` WHERE `COLLECTION` = '".$nCollection."'");
           	for($j=0; $j<count($arElement); $j++)
           	{
          		$LIB['COMPONENT_ELEMENT']->Delete($arElement[$j]['ID'], $nComponent);
           	}

           	$arElement = $DB->Rows("SELECT ID FROM `k2_component".$nComponent."category` WHERE `COLLECTION` = '".$nCollection."'");
           	for($j=0; $j<count($arElement); $j++)
           	{
          		$LIB['COMPONENT_CATEGORY']->Delete($arElement[$j]['ID'], $nComponent);
           	}
           	$DB->Query("DELETE FROM `k2_component_field` WHERE `COMPONENT` = '".$nComponent."' AND `COLLECTION` = '".$nCollection."'");
       }
	}

	function CreateIcon($arPar)
	{
   		if(!$arPar['ID'] || !$arPar['NAME']){
   			return false;
   		}
   		$sIcon = 'default';
   		if($arPar['ICON'] && file_exists($_SERVER['DOCUMENT_ROOT'].'/k2/admin/i/component/'.$arPar['ICON'].'.png')){        	$sIcon = $arPar['ICON'];
   		}
   		$sPathIcon = $_SERVER['DOCUMENT_ROOT'].'/k2/admin/i/component/'.$sIcon.'.png';
   		$nWidth = 105;
		$nHeight = 25;

		$rImage = imagecreatetruecolor(500, $nHeight);
		$rColor = imageColorAllocate($rImage, 0, 0, 0);
		$arList = imagettftext($rImage, 8, 0, 0, 0, $rColor, $_SERVER['DOCUMENT_ROOT'].'/k2/lib/tool/ttf/verdana.ttf', $arPar['NAME']);
		$arList[2] += 44;
		if($arList[2]>$nWidth){
			$nWidth = $arList[2];
		}
		$rImage = imagecreatetruecolor($nWidth, $nHeight);
		$rImage1 = imagecreatefromgif($_SERVER['DOCUMENT_ROOT'].'/k2/lib/component/icon/left.gif');
		$rImage2 = imagecreatefromgif($_SERVER['DOCUMENT_ROOT'].'/k2/lib/component/icon/center.gif');
		$rImage3 = imagecreatefromgif($_SERVER['DOCUMENT_ROOT'].'/k2/lib/component/icon/right.gif');
		$rImage4 = imagecreatefrompng($sPathIcon);
		imagecopy($rImage, $rImage2, 0, 0, 0, 0, $nWidth, 25);
		imagecopy($rImage, $rImage1, 0, 0, 0, 0, 3, $nHeight);
		imagecopy($rImage, $rImage3, $nWidth-3, 0, 0, 0, 3, 25);
		imagecopy($rImage, $rImage4, 10, 5, 0, 0, 16, 16);
		imagettftext($rImage, 8, 0, 34, 16, $rColor, $_SERVER['DOCUMENT_ROOT'].'/k2/lib/tool/ttf/verdana.ttf', $arPar['NAME']);
		imagegif($rImage, $_SERVER['DOCUMENT_ROOT'].'/k2/dev/component/'.$arPar['ID'].'/static/icon.gif');
		imagedestroy($rImage);
		return true;
	}

	function IncludeTemplate($nComponent)
	{
    	global $LIB, $MOD, $DB, $CURRENT, $USER;
    	$sFile = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/component/'.(int)$nComponent.'/template.php';
        if(file_exists($sFile)){
        	ob_start();
			include($sFile);
			$sCont = ob_get_contents();
			ob_end_clean();
        	return $sCont;
        }
	}

	function IncludeComponent($sText)
	{
 		global $DB, $LIB, $CURRENT;
 		preg_match_all("#<!-- component(\d+):(\d+) -->#", $sText, $arMath);
		for($i=0; $i<count($arMath[1]); $i++)
		{
        	if($arComponent = $this->ID($arMath[1][$i], false)){
               	$CURRENT['COMPONENT'] = $arComponent;
               	$CURRENT['COMPONENT']['COLLECTION'] = $arMath[2][$i];
               	$template = $this->IncludeTemplate($arMath[1][$i]);
               	$sText = str_replace('<!-- component'.$arMath[1][$i].':'.$arMath[2][$i].' -->', $template, $sText);
         	}
		}
		return $sText;
	}
}
?>