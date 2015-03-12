<?
class Field
{
	function Rows($sTable)
	{
    	global $DB;
    	return $DB->Rows("SELECT * FROM `k2_field` WHERE `TABLE` = '".DBS($sTable)."' ORDER BY `SORT` ASC");
	}

	function ID($nID)
	{
		global $LIB, $DB;
		if($arRow = $DB->Row("SELECT * FROM `k2_field` WHERE `ID` = '".$nID."'")){
			$arRow['SETTING'] = unserialize($arRow['SETTING']);
			return $arRow;
        }
        $this->Error = 'Поле не найдено';
		return false;
	}

	function Add($sTable, $arPar = array())
	{
		global $LIB, $DB, $USER;

        if($sError = formCheck(array('FIELD' => 'Название поля', 'NAME' => 'Описание'), $arPar)){
       		$this->Error = $sError;
			return false;
        }

		if(!preg_match("#^[a-z0-9_]+$#i", $arPar['FIELD'])){
        	$this->Error = 'Название поле может состоять из набора символов a-z0-9_';
			return false;
		}

		$sType = 'VARCHAR(255) NOT NULL';
		if($arPar['TYPE'] == 1){
			$sType = 'MEDIUMTEXT NOT NULL';
		}
		if($arPar['TYPE'] == 2){
			$sType = 'CHAR(1) NOT NULL';
		}
		if($arPar['TYPE'] == 3 || $arPar['TYPE'] == 5){
			$sType = 'TEXT NOT NULL';
		}

		if(!$DB->Query("ALTER TABLE `".DBS($sTable)."` ADD `".$arPar['FIELD']."` ".$sType)){
	        $this->Error = 'Поле с таким названием уже существует';
	        return false;
	    }
        $nSort = 10;
	    if($arField = $DB->Rows("SELECT `SORT` FROM `k2_field` WHERE `TABLE` = '".DBS($sTable)."' ORDER BY `SORT` DESC LIMIT 1")){
			$nSort = $arField[0]['SORT']+10;
		}

		if($nID = $DB->Insert("
		INSERT INTO `k2_field`(
			`TABLE`,
			`NAME`,
			`FIELD`,
			`SORT`,
			`TYPE`,
			`REQUIRED`,
			`MULTIPLE`,
			`SETTING`
		)VALUES(
			'".DBS($sTable)."', '".DBS($arPar['NAME'])."', '".DBS($arPar['FIELD'])."', '".$nSort."', '".(int)$arPar['TYPE']."', '".(int)$arPar['REQUIRED']."', '".(int)$arPar['MULTIPLE']."', '".DBS(serialize($arPar['SETTING']))."'
		)")){
	    	return $nID;
		}
    	return false;
	}

	function Edit($nID, $arPar = array(), $bFull = 0)
	{
		global $LIB, $DB, $USER;

        if(!$arField = $this->ID($nID)){
        	return false;
        }
        if(!$bFull){
        	$arPar += $arField;
        }
		if($sError = formCheck(array('NAME' => 'Описание'), $arPar)){
       		$this->Error = $sError;
			return false;
        }
		if($DB->Query("UPDATE `k2_field`
	    SET
			`NAME` = '".DBS($arPar['NAME'])."',
			`REQUIRED` = '".(int)$arPar['REQUIRED']."',
			`MULTIPLE` = '".(int)$arPar['MULTIPLE']."',
			`SETTING` = '".DBS(serialize($arPar['SETTING']))."',
			`SORT` = '".(int)$arPar['SORT']."'
	    WHERE
	    	ID = '".$nID."';
	    ")){
			return true;
	    }

    	return false;
	}

	function Delete($nID)
	{
    	global $LIB, $DB, $USER;

    	if(!$arField = $this->ID($nID)){
        	return true;
        }
        $LIB['FILE']->DeleteAll(array('TABLE' => 'k2_block'.$arField['ID']));
        $LIB['FILE']->DeleteAll(array('TABLE' => 'k2_block'.$arField['ID'].'category'));

		$DB->Query("ALTER TABLE `".$arField['TABLE']."` DROP `".$arField['FIELD']."`");
		$DB->Query("DELETE FROM `k2_field` WHERE `ID` = '".$nID."'");
		return true;
	}

	function DeleteContent($arPar)
	{    	global $DB, $LIB;

		$QB = new QueryBuilder;
	    $QB->From('k2_field')->Select('`ID`, `FIELD`, `TYPE`')->Where('(`TYPE` = 1 OR `TYPE` = 4) AND `TABLE` = ?', $arPar['TABLE']);
	    if($arPar['FIELD_NAME']){
	    	$QB->Where('ID = ?', $arPar['FIELD_NAME']);
	    }
		$arField = $DB->Rows($QB->Build());

        $QB = new QueryBuilder;
		$QB->From($arPar['TABLE']);
        if($arPar['ELEMENT']){
	    	$QB->Where('ID = ?', $arPar['ELEMENT']);
	    }
	    for($i=0; $i<count($arField); $i++)
		{
			$QB->Select($arField[$i]['FIELD']);
		}
		$arElement = $DB->Rows($QB->Build());
        for($i=0; $i<count($arElement); $i++)
		{
			for($j=0; $j<count($arField); $j++)
			{
				if($arField[$j]['TYPE'] == 1){
					$LIB['COMPONENT']->FindAndDelete(array('FIELD' => $arField[$j]['FIELD'], 'TEXT' => $arElement[$i][$arField[$j]['FIELD']]));
				}
				if($arField[$j]['TYPE'] == 4){					$LIB['FILE']->Delete($arElement[$i][$arField[$j]['FIELD']]);
				}
			}
		}
	}

	function Type()
	{
    	return array(
	    'Строка',
	    'Текстовая область',
	    'Истина или ложь',
	    'Список',
	    'Файл',
	    'Связь',
	    'Скрытое');
	}

	function CheckAll($sTable, $arPar)
	{
	    $arField = $this->Rows($sTable);
		for($i=0; $i<count($arField); $i++)
		{
			if($sError = $this->Check($arField[$i], $arPar)){
				return $sError;
			}
		}
	}

	function Check($arField, $arPar)
	{		global $LIB;

 		$arSetting = unserialize($arField['SETTING']);

        $arValue[0] = $arPar[$arField['FIELD']];
        if($arField['MULTIPLE']){        	$arValue = $arPar[$arField['FIELD']];
        }

		if($arField['TYPE'] != 4){
			if($arField['REQUIRED'] && !$arValue[0]){
				return changeMessage($arField['NAME']);
			}
		}

		if($arField['TYPE'] == 0){
	 		if($arSetting['TYPE'] == 3){
	 			$arValue[0] = str_replace('http://', '', $arValue[0]);
	 		}

			if($arValue[0]){				if($arSetting['TYPE'] == 1){
		    		if(!preg_match("#^[0-9]$#", trim($arValue[0]))){
		                return 'В поле &laquo;'.$arField['NAME'].'&raquo; введите число';
		    		}
				}
				if($arSetting['TYPE'] == 2){
		    		if(!preg_match("#^[0-9](\.[0-9])?$#", $arValue[0])){
		                return 'В поле &laquo;'.$arField['NAME'].'&raquo; введите число';
		    		}
				}
				if($arSetting['TYPE'] == 3){
		    		if(!preg_match("#[a-z0-9\-/\.]+\.[a-z]{1,4}(/)?$#i", $arValue[0])){
		                return 'В поле &laquo;'.$arField['NAME'].'&raquo; укажите верный адрес сайта';
		    		}
				}
				if($arSetting['TYPE'] == 4){
		    		if(!preg_match("#^[a-z0-9\._\-]+@[a-z0-9\._\-]+\.[a-z]{2,4}$#i", $arValue[0])){
		                return 'В поле &laquo;'.$arField['NAME'].'&raquo; введите корректный адрес эл. почты';
		    		}
				}

				if($arSetting['TYPE'] == 5){
		    		if(!preg_match("#^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$#i", $arValue[0])){
		                return 'Неправильная дата в поле &laquo;'.$arField['NAME'].'&raquo;';
		    		}
				}
			}
	 	}



		if($arField['TYPE'] == 4){
            if($arField['MULTIPLE']){            	$__FILES = $_FILES[$arField['FIELD']];
            }else{            	$__FILES = array(
            	'name' => array($_FILES[$arField['FIELD']]['name']),
            	'type' => array($_FILES[$arField['FIELD']]['type']),
            	'tmp_name' => array($_FILES[$arField['FIELD']]['tmp_name']),
            	'error' => array($_FILES[$arField['FIELD']]['error']),
            	'size' => array($_FILES[$arField['FIELD']]['size']),
            	);
            }

            if($arField['REQUIRED'] && !$__FILES['tmp_name'][0]){
            	if(count($_POST['FILE_OLD'][$arField['ID']]) <= count($_POST['FILE_DELETE'][$arField['ID']])){            		return 'Загрузите файл в поле &laquo;'.$arField['NAME'].'&raquo;';
            	}
			}

			for($i=0; $i<count($__FILES['tmp_name']); $i++)
			{
				if(!$__FILES['tmp_name'][$i]){					continue;
				}

				if($arSetting['FILESIZE']>0 && $arSetting['FILESIZE']<$__FILES['size'][$i]){
		            return 'В поле &laquo;'.$arField['NAME'].'&raquo; файл слишком большого размера';
		        }
				preg_match("#.+\.(.+?)$#i", $__FILES['name'][$i], $arMath);
		        $arMath[1] = strtolower($arMath[1]);
		        if($arSetting['FILE_EXT']){
					$arExt = str_replace(' ', '', explode(',', $arSetting['FILE_EXT']));
		            if(!in_array($arMath[1], $arExt)){
						return 'В поле &laquo;'.$arField['NAME'].'&raquo; неверный тип файла';
		            }
		        }
				#Если указаны минимальные размеры загружаемой картинки запускаем механизм
				if($arSetting['MIN']['WIDTH'] || $arSetting['MIN']['HEIGHT']){
	            	$tmpName = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5(microtime()).'.'.$arMath[1];
	                if(is_uploaded_file($__FILES['tmp_name'][$i]) && @copy($__FILES['tmp_name'][$i], $tmpName)){
	                    $arPhotoProp = @getimagesize($tmpName);
	                    unlink($tmpName);
	                    if(($arPhotoProp[0]<$arSetting['MIN']['WIDTH'] && $arSetting['MIN']['WIDTH']) || ($arPhotoProp[1]<$arSetting['MIN']['HEIGHT'] && $arSetting['MIN']['HEIGHT'])){
	                    	if($arSetting['MIN']['WIDTH'] && $arSetting['MIN']['HEIGHT']){
	                    		return 'Размеры файла в поле &laquo;'.$arField['NAME'].'&raquo; должны быть не менее '.$arSetting['MIN']['WIDTH'].'x'.$arSetting['MIN']['HEIGHT'].'px';
	                    	}elseif($arSetting['MIN']['WIDTH']){
	                       		return 'Размер файла в поле &laquo;'.$arField['NAME'].'&raquo; по ширине должны быть не менее '.$arSetting['MIN']['WIDTH'].'px';
	                    	}elseif($arSetting['MIN']['HEIGHT']){
	                        	return 'Размер файла в поле &laquo;'.$arField['NAME'].'&raquo; по высоте должны быть не менее '.$arSetting['MIN']['HEIGHT'].'px';
	                    	}
	                    }
	                }
		        }
			}
		}
	}

	function Update($arList, $arPar)
	{    	global $DB, $LIB;

    	$QB = new QueryBuilder;
    	$QB->Update($arList['TABLE']);

    	$arField = $this->Rows($arList['TABLE']);
	    for($i=0; $i<count($arField); $i++)
		{
			$arSetting = unserialize($arField[$i]['SETTING']);

	        $sValue = $arPar[$arField[$i]['FIELD']];
            if($arField[$i]['MULTIPLE'] && is_array($sValue)){            	$sValue = implode(',', $sValue);
            	if($sValue){            		$sValue = ','.$sValue.',';
            	}
            }

            if($arField[$i]['TYPE'] == 0){            	if($sValue){	            	if($arSetting['TYPE'] == 5){
			    		if(preg_match("#^(\d{2})\.(\d{2})\.(\d{4}) (\d{2}):(\d{2})$#i", $sValue, $arMath)){
			                $sValue = $arMath[3].'-'.$arMath[2].'-'.$arMath[1].' '.$arMath[4].':'.$arMath[5].':00';
			    		}
					}
            	}
	        }

            if($arField[$i]['TYPE'] == 4){
				if($arField[$i]['MULTIPLE']){
	            	$__FILES = $_FILES[$arField[$i]['FIELD']];
	            }else{
	            	$__FILES = array(
	            	'name' => array($_FILES[$arField[$i]['FIELD']]['name']),
	            	'type' => array($_FILES[$arField[$i]['FIELD']]['type']),
	            	'tmp_name' => array($_FILES[$arField[$i]['FIELD']]['tmp_name']),
	            	'error' => array($_FILES[$arField[$i]['FIELD']]['error']),
	            	'size' => array($_FILES[$arField[$i]['FIELD']]['size'])
	            	);
	            	if($arPar['FILE_DELETE'][$arField[$i]['ID']]){
	            		$arPar['FILE_DELETE'][$arField[$i]['ID']] = array($arPar['FILE_OLD'][$arField[$i]['ID']]);
	            	}
	            	if($arPar['FILE_OLD'][$arField[$i]['ID']]){
	            		$arPar['FILE_OLD'][$arField[$i]['ID']] = array($arPar['FILE_OLD'][$arField[$i]['ID']]);
	            	}
	            	if($_FILES[$arField[$i]['FIELD']]['name'] && $arField[$i]['REQUIRED'] && $arPar['FILE_OLD'][$arField[$i]['ID']] && !$arPar['FILE_DELETE'][$arField[$i]['ID']]){	            		$arPar['FILE_DELETE'][$arField[$i]['ID']] = $arPar['FILE_OLD'][$arField[$i]['ID']];
	            	}
	            }

                $arAllFile = array();
                for($j=0; $j<count($arPar['FILE_OLD'][$arField[$i]['ID']]); $j++)
                {
                	if($arPar['FILE_DELETE'][$arField[$i]['ID']][$j]){
						$LIB['FILE']->Delete($arPar['FILE_DELETE'][$arField[$i]['ID']][$j]);
					}else{						$arAllFile[] = $arPar['FILE_OLD'][$arField[$i]['ID']][$j];
					}
                }

                for($j=0; $j<count($__FILES['tmp_name']); $j++)
				{
					if(!$__FILES['tmp_name'][$j]){
						continue;
					}
	                $arParFile = array('name' => $__FILES['name'][$j], 'type' => $__FILES['type'][$j], 'tmp_name' => $__FILES['tmp_name'][$j], 'error' => $__FILES['error'][$j], 'size' => $__FILES['size'][$j]);
			        $arParFile['PATH'] = $arList['PATH'];
					if($arSetting['RESIZE']){
						$arParFile['WIDTH'] = $arSetting['RESIZE']['WIDTH'];
		                $arParFile['HEIGHT'] = $arSetting['RESIZE']['HEIGHT'];
		                $arParFile['FIX'] = $arSetting['RESIZE']['FIX'];
					}
	                if($arSetting['PREVIEW']['SHOW']){
	                	$arParFile['PREVIEW'] = $arSetting['PREVIEW'];
	                }
	                $arParFile['TRANSLATION'] = $arSetting['TRANSLATION'];
			        if($nFileID = $LIB['FILE']->Upload($arParFile)){
						$arSetting = unserialize($arField[$i]['SETTING']);
						$arAllFile[] = $nFileID;
					}
				}
				$sValue = '';
				if($arAllFile){					$sValue = implode(',', $arAllFile);
	            	if($sValue && $arField[$i]['MULTIPLE']){
	            		$sValue = ','.$sValue.',';
	            	}
				}
		    }

		    if($arField[$i]['TYPE'] == 4 && !$sValue && !$arPar['FILE_OLD'][$arField[$i]['ID']]){
			}else{				$QB->Set($arField[$i]['FIELD'].' = ?', $sValue);
			}
		}
		$QB->Where('ID = ?', $arList['ID']);
		$DB->Query($QB->Build());

		if($arField[$i]['TYPE'] == 1){
            $LIB['COMPONENT']->DeleteUnused($arField[$i]['ID']);
        }
	}
}
?>