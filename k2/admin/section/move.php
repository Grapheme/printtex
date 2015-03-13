<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('SECTION');

if(!$_GET['code']){
	exit;
}

if(!$arSectionFrom = $LIB['SECTION']->ID($_GET['section_from'])){
	exit;
}
if(!$arSectionTo = $LIB['SECTION']->ID($_GET['section_to'])){
	exit;
}

if($_GET['code'] == 1){    $nSort = 0;
	if($arRow = $DB->Row("SELECT `SORT` FROM `k2_section` WHERE `PARENT` = '".$arSectionTo['ID']."' ORDER BY `SORT` DESC LIMIT 1")){		$nSort = $arRow['SORT'] + 10;
	}
    $arSectionFrom['PARENT'] = $arSectionTo['ID'];
    $arSectionFrom['SORT'] = $nSort;
    $LIB['SECTION']->Edit($arSectionFrom['ID'], $arSectionFrom);
}

if($_GET['code'] == 2){
	$arSectionChild = $LIB['SECTION']->Child(array('ID' => $arSectionTo['PARENT']));
	for($i=0, $j=0; $i<count($arSectionChild); $i++)
	{
        $arSectionChild[$i]['SORT'] = $j;
    	if($arSectionChild[$i]['ID'] == $arSectionTo['ID']){
        	$j += 10;
        	$arSectionFrom['SORT'] = $j;
        	$arSectionFrom['PARENT'] = $arSectionTo['PARENT'];
    	}
    	$LIB['SECTION']->Edit($arSectionChild[$i]['ID'], $arSectionChild[$i]);
    	$j += 10;
	}
	$LIB['SECTION']->Edit($arSectionFrom['ID'], $arSectionFrom);
}

Redirect($_SERVER['HTTP_REFERER']);
?>