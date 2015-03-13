<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class Form
{
	function Element($nID, $sTemplate = '%FIELD%')
	{
		global $DB, $LIB, $ADMIN_PANEL;
  		if(!$arField = $LIB['FIELD']->ID($nID)){  			return false;
  		}

		$sID = 'f'.$nID;

        if(isset($_POST[$arField['FIELD']])){
			$arField['VALUE'] = $_POST[$arField['FIELD']];
		}

        if($arField['TYPE'] == 0){
        	if($arField['SETTING']['TYPE'] == 3 && !isset($_POST[$arField['FIELD']])){        		$arField['VALUE'] = 'http://';
        	}

        	if($arField['SETTING']['TYPE'] == 5 && $arField['SETTING']['DEFAULT_DATE']){
        		if(!isset($_POST[$arField['FIELD']])){        			$arField['VALUE'] = date('d.m.Y H:i');
        		}else{
        			if(preg_match("#^(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2})#i", $arField['VALUE'], $arMath)){        				$arField['VALUE'] = $arMath[3].'.'.$arMath[2].'.'.$arMath[1].' '.$arMath[4].':'.$arMath[5];
			    	}
        		}
        	}

        	$sField = '<input type="text" name="'.$arField['FIELD'].'" value="'.html($arField['VALUE']).'" id="'.$sID.'">';

        	if($arField['SETTING']['TYPE'] == 5){
        		$sField .= '<img src="/k2/admin/i/icon/calendar.gif" width="16" height="16" class="calendar" title="Выбрать дату" id="f'.$sID.'_trigger" style="position:absolute; margin:4px 0 0 8px">
        		<script type="text/javascript" src="/k2/admin/calendar/js/jscal2.js"></script>
        		<script type="text/javascript" src="/k2/admin/calendar/js/lang/ru.js"></script>
        		<link type="text/css" rel="stylesheet" href="/k2/admin/calendar/css/jscal2.css">
        		<script type="text/javascript">
	            new Calendar({
	            	inputField:"'.$sID.'",
					dateFormat:"%d.%m.%Y %H:%I",
					trigger:"f'.$sID.'_trigger",
					bottomBar:false,
					showTime:1,
					animation:false,
					onSelect:function(){
						this.hide();
					}
				});
				</script>';
        	}
        }

        if($arField['TYPE'] == 1){
        	$sField = '<textarea name="'.$arField['FIELD'].'" rows="6"';
        	if($arField['SETTING']['WYSIWYG']){        		$sField .= ' class="tinymce"';
        	}
        	if($ADMIN_PANEL){        		$sField .= ' field_id="'.$arField['ID'].'"';
        	}
        	$sField .= ' id="'.$sID.'">'.html($arField['VALUE']).'</textarea>';
        }

        if($arField['TYPE'] == 2){
        	if($arField['SETTING']['VIEW']){
            	$sField .= '<label><input type="radio" name="'.$arField['FIELD'].'" value="1" checked="checked"';
					if($arField['SETTING']['DEFAULT'] || $arField['VALUE']){
	            		$sField .= ' checked="checked"';
	            	}
					$sField .= '>Да</label>
					<label><input type="radio" name="'.$arField['FIELD'].'" value="0"';
					if(!$arField['SETTING']['DEFAULT'] && !$arField['VALUE']){
	            		$sField .= ' checked="checked"';
	            	}
					$sField .= '>Нет</label>';
        	}else{
            	$sField = '<input type="hidden" name="'.$arField['FIELD'].'" value="0"><label>
	            	<input type="checkbox" name="'.$arField['FIELD'].'" value="1"';
	            	if($arField['SETTING']['DEFAULT'] || $arField['VALUE']){
	            		$sField .= ' checked="checked"';
	            	}
	            	$sField .= '>'.$arField['NAME'].'</label>';
	        	$arField['NAME'] = '';
        	}
        }

        if($arField['TYPE'] == 3){
            if(!$arSelect = $LIB['SELECT']->ID($arField['SETTING']['SELECT'])){
				return false;
			}
			if($arField['MULTIPLE']){
                $arValue = $arField['VALUE'];
				if(!is_array($arField['VALUE'])){					$arValue = clearArray(explode(',', $arField['VALUE']));
				}
			}else{				$arValue[0] = $arField['VALUE'];
			}
			$sField .= '<input type="hidden" name="'.$arField['FIELD'].'" value="">';
			if($arField['MULTIPLE']){
            	$sField .= '<select name="'.$arField['FIELD'].'[]" multiple size="6">';
            }else{            	$sField .= '<select name="'.$arField['FIELD'].'"><option value="0">Выбрать</option>';
            }
		  	for($i=0; $i<count($arSelect['OPTION']); $i++)
		   	{
		    	$sField .= '<option value="'.$arSelect['OPTION'][$i]['ID'].'"';
		     	if(in_array($arSelect['OPTION'][$i]['ID'], $arValue)){
		            $sField .= ' selected';
		      	}
		        $sField .= '>'.html($arSelect['OPTION'][$i]['NAME']).'</option>';
			}
			$sField .= '</select>';
        }

        if($arField['TYPE'] == 4){        	if($arField['MULTIPLE']){
				$sField .= '<input type="file" name="'.html($arField['FIELD']).'[]" class="fileMultiple">';

				$arValue = clearArray(explode(',', $arField['VALUE']));
				if($_POST['FILE_OLD'][$arField['ID']]){
	        		$arValue = $_POST['FILE_OLD'][$arField['ID']];
	        	}

				for($i=0; $i<count($arValue); $i++)
				{
					if($arFile = $LIB['FILE']->ID($arValue[$i])){
					    $sField .= '<div><input type="hidden" name="FILE_OLD['.$arField['ID'].']['.$i.']" value="'.$arValue[$i].'">
					    <label><input type="checkbox" name="FILE_DELETE['.$arField['ID'].']['.$i.']" value="'.$arValue[$i].'" ';
					    if($_POST['FILE_DELETE'][$arField['ID']][$i] == $arValue[$i]){
					    	$sField .= ' checked';
					    }
					    $sField .= ' id="'.$sID.'_'.$i.'" title="Удалить"></label><a href="'.$arFile['PATH'].'" target="_blank">'.html($arFile['NAME']).'</a> (';
					    if($arFile['WIDTH']){					    	$sField .= $arFile['WIDTH'].'x'.$arFile['HEIGHT'].', ';
					    }
					    $sField .= fileByte($arFile['SIZE']).')</div>';
					}
				}
        	}else{	         	if($_POST['FILE_OLD'][$arField['ID']]){
	        		$arField['VALUE'] = $_POST['FILE_OLD'][$arField['ID']];
	        	}
	        	$sField .= '<input type="file" name="'.html($arField['FIELD']).'">';
				if($arField['VALUE']){
					if($arFile = $LIB['FILE']->ID($arField['VALUE'])){
						$sField .= '<div><input type="hidden" name="FILE_OLD['.$arField['ID'].']" value="'.$arField['VALUE'].'">
						<label><input type="checkbox" name="FILE_DELETE['.$arField['ID'].']" value="1" ';
					    if($_POST['FILE_DELETE'][$arField['ID']]){
					    	$sField .= ' checked="checked"';
					    }
					    $sField .= ' id="'.$sID.'_'.$i.'" title="Удалить"></label><a href="'.$arFile['PATH'].'" target="_blank">'.html($arFile['NAME']).'</a> (';
					    if($arFile['WIDTH']){
					    	$sField .= $arFile['WIDTH'].'x'.$arFile['HEIGHT'].', ';
					    }
					    $sField .= fileByte($arFile['SIZE']).')</div>';
					}
				}
        	}
        }

        if($arField['TYPE'] == 5){
			if($arField['MULTIPLE']){
                $arValue = $arField['VALUE'];
				if(!is_array($arField['VALUE'])){
					$arValue = clearArray(explode(',', $arField['VALUE']));
				}
			}else{
				$arValue[0] = $arField['VALUE'];
			}

			$sField .= '<input type="hidden" name="'.$arField['FIELD'].'" value="">';
			if($arField['MULTIPLE']){
            	$sField .= '<select name="'.$arField['FIELD'].'[]" multiple size="6">';
            }else{
            	$sField .= '<select name="'.$arField['FIELD'].'"><option value="0">Выбрать</option>';
            }

			if($arField['SETTING']['TYPE'] == 0){
           		$arSection = $LIB['SECTION']->Child(array('RECURSIVE' => 1));
			  	for($i=0; $i<count($arSection); $i++)
			   	{
			    	$sField .= '<option value="'.$arSection[$i]['ID'].'"';			     	if(in_array($arSection[$i]['ID'], $arValue)){
			            $sField .= ' selected';
			      	}
			        $sField .= '>'.str_repeat('&mdash;', $arSection[$i]['LEVEL']+1).' '.html($arSection[$i]['NAME']).'</option>';
				}
			}elseif($arField['SETTING']['TYPE'] == 1){
            	$arUser = $LIB['USER']->Rows();
			  	for($i=0; $i<count($arUser); $i++)
			   	{
			    	$sField .= '<option value="'.$arUser[$i]['ID'].'"';					if(in_array($arUser[$i]['ID'], $arValue)){
			            $sField .= ' selected';
			      	}
			        $sField .= '>'.html($arUser[$i]['LOGIN']).'</option>';
				}
			}
			$sField .= '</select>';
        }

        if($arField['TYPE'] == 6){
        	$sField = '<input type="hidden" name="'.$arField['FIELD'].'" value="'.html($arField['VALUE']).'">';
        }

		if($arField['TYPE'] != 6){			$sName = html($arField['NAME']);
	        if($arField['REQUIRED']){
	        	$sName .= '<span class="star">*</span>';
	        }
	        $sField = str_replace("%FIELD%", $sField, str_replace("%NAME%", $sName, $sTemplate));
		}
		return $sField;
	}
}
?>