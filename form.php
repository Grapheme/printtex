<?
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){	$arSite = $LIB['SITE']->ID(1);	$arField = $LIB['FIELD']->Rows('k2_block34');
	$_POST['ACTIVE'] = 1;
	if($nID = $LIB['BLOCK_ELEMENT']->Add(65, $_POST)){
		$sBody  = "Новая заявка\n\n";
           for($i=0; $i<count($arField); $i++)
		{
			$sValue = $_POST[$arField[$i]['FIELD']];
			if($arField[$i]['TYPE'] == 3){
            	if($arRows = $DB->Row("SELECT NAME FROM `k2_select_option` WHERE ID = '".$sValue."'")){
            		$sValue = $arRows['NAME'];
            	}
			}
			$sBody .= $arField[$i]['NAME'].": ".($sValue?$sValue:'-')."\n";
		}
        $sBody .= "\n-------\nДата отправки: ".date('d-m-Y, H:i');
		sendMail($arSite['EMAIL'], $arSite['EMAIL'], $sBody);
	}else{		echo $LIB['BLOCK_ELEMENT']->Error;
	}
}
?>