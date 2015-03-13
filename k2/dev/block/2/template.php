<?
$arRows = $LIB['SECTION']->Map(array('SITE' => $CURRENT['SITE']['ID'], 'ACTIVE' => 1));
for($i=0; $i<count($arRows); $i++)
{
	if($arRows[$i]['EXTERNAL']){
		$arRows[$i]['URL'] = $arRows[$i]['EXTERNAL'];
	}
	if(!$arRows[$i]['LEVEL']){
    	?><div><a href="<?=$arRows[$i]['URL']?>"><?=$arRows[$i]['NAME']?></a></div><?
	}else{
   		?><div style="padding-left:<?=$arRows[$i]['LEVEL']*20?>px">&mdash;&nbsp;&nbsp;<a href="<?=$arRows[$i]['URL']?>"><?=$arRows[$i]['NAME']?></a></div><?
	}
}
?>