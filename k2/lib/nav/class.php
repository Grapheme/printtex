<?
/**
* @copyright (C) 2005-2011 K2CMS
* @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
*/

class Nav
{
	function Rows()
	{
    	global $DB;
    	$arNav = $DB->Rows("SELECT * FROM `k2_nav` ORDER BY ID ASC");
		return $arNav;
	}

	function ID($nID)
	{
		global $LIB, $DB;
		if($arNav = $DB->Row("SELECT * FROM `k2_nav` WHERE `ID` = '".$nID."'")){
        	$arNav['TEMPLATE'] = $LIB['FILE']->Read('/k2/dev/nav/'.$nID.'/template.php');
			return $arNav;
        }
        $this->Error = 'Шаблон навигации не найден';
		return false;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB, $USER;

		if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }

		if($nID = $DB->Insert("
		INSERT INTO `k2_nav` (
			`NAME`
		)VALUES(
			'".DBS($arPar['NAME'])."'
		);
		")){
        	$arExs[] = $LIB['FILE']->Create('/k2/dev/nav/'.$nID.'/template.php', $arPar['TEMPLATE']);
			if(in_array('', $arExs)){
				$DB->Query("DELETE FROM `k2_nav` WHERE ID = '".$nID."'");
				$this->Error = $LIB['FILE']->Error;
			}else{
            	return $nID;
			}
		}

    	return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB, $USER;

        if(!$arNav = $this->ID($nID)){
        	return false;
        }

        if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }

        if($DB->Query("UPDATE `k2_nav`
        SET
			`NAME` = '".DBS($arPar['NAME'])."'
        WHERE
        	`ID` = '".$nID."';
        ")){
	        $arExs[] = $LIB['FILE']->Edit('/k2/dev/nav/'.$nID.'/template.php', $arPar['TEMPLATE']);
	        if(in_array('', $arExs)){
				$this->Error = $LIB['FILE']->Error;
				return false;
			}else{
            	return $nID;
			}
        }

    	return false;
	}

	function Delete($nID)
	{
    	global $LIB, $DB;
        unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.$nID.'/template.php');
        rmdir($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.$nID);
		$LIB['FILE']->Delete('/k2/dev/nav/'.$nID.'/');
		$DB->Query("DELETE FROM `k2_nav` WHERE ID = '".$nID."'");
		return true;
	}

	function Page($mTemplate, $bReturn = false)
	{	    global $LIB, $MOD, $DB, $CURRENT;

		$sPath = '/k2/dev/nav/'.(int)$mTemplate.'/template.php';
		if(is_array($mTemplate)){        	$sPath = $mTemplate['PATH'];
		}

		$arSetting = $this->Setting;
		$arSetting['CURRENT'] = (int)$_GET['page'];
        if(!$arSetting['CURRENT']){        	$arSetting['CURRENT'] = 1;
        }
	    $arList = array();
	    $bCurrent = 0;

		if($this->Setting['TOTAL'] && $this->Setting['SIZE']){
			for($i=0; $i<ceil(($this->Setting['TOTAL'])/$this->Setting['SIZE']); $i++)
			{
				$arList[$i]['URL'] = urlQuery(array('page' => ($i+1)));
				if($arSetting['CURRENT'] == $i+1){
					$arList[$i]['CURRENT'] = 1;
					$bCurrent = 1;
				}
			}
			if(!$bCurrent){				$arList[0]['CURRENT'] = 1;
			}
			if(count($arList)<2){
				$arList = array();
			}
		}
		$this->Setting['PAGES'] = 0;
	    if($arList){
		    $arSetting['PAGES'] = $this->Setting['PAGES'] = count($arList);
		    unset($nNum, $bCurrent);
		    include($_SERVER['DOCUMENT_ROOT'].$sPath);
		    if($bReturn){		    	return $sTemplate;
		    }
	    }
	}

	function Back($nTemplate)
	{
     	?><!-- $NAV<?=(int)$nTemplate?>$ --><?
        $GLOBALS['NAV'][] = $nTemplate;
	}

	function BackAdd($nTemplate, $arPar)
	{
     	$GLOBALS['NAV_ADD'][$nTemplate][] = $arPar;
	}

	function BackResult($nTemplate)
	{
     	global $LIB, $MOD, $DB, $CURRENT;

		$arList = $LIB['SECTION']->Back(array('ID' => $CURRENT['SECTION']['ID']));

		for($i=0; $i<count($GLOBALS['NAV_ADD'][$nTemplate]); $i++)
	    {
	    	$arList[] = array('NAME' => $GLOBALS['NAV_ADD'][$nTemplate][$i][0], 'URL' => $GLOBALS['NAV_ADD'][$nTemplate][$i][1]);
	    }

		for($i=0; $i<count($arList); $i++)
	    {
	    	$arList[$i]['CURRENT'] = ($CURRENT['SECTION']['URL'] == $arList[$i]['URL']);
	    }

	    ob_start();
		include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.(int)$nTemplate.'/template.php');
		$sCont = ob_get_contents();
		ob_end_clean();

		return $sCont;
	}

	function Menu($nTemplate, $arPar = array())
	{
     	global $LIB, $MOD, $DB, $CURRENT;

		if(!$arPar['PARENT']){
			$arPar['PARENT'] = 0;
		}

		$arFilter = array(
        'FROM' => 'k2_section',
        'WHERE' => $arPar,
        'ORDER_BY' =>  array('SORT' => 'ASC')
        );
        $arList = $DB->Rows($DB->CSQL($arFilter));
		for($i=0; $i<count($arList); $i++)
	    {
	    	$arList[$i]['CURRENT'] = (strpos($CURRENT['SECTION']['URL'], $arList[$i]['URL']) !== false);
	    }
		include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.(int)$nTemplate.'/template.php');
	}
}
?>