<?
$arTod = array('ночью', 'утром', 'днем', 'вечером');
if(!$arWeather = $MOD['CACHE']->GetVar(14400, 'weather'.serialize($MOD_SETTING['WEATHER']))){
	$arWeather = $MOD['WEATHER']->Rows();
	/*
	Матрица соответствий погодных условий иконкам
	1 - день | ясно        |                    1, 2, 0, -
	2 -      | малооблачно |                    2, -, 1, -
	3 -      | облачно     |                    3, -, 2, -
	4 - день | малооблачно |                    4, 2, 1, -
	5 - день | пасмурно    |                    5, 2, 3, -
	6 -      |             | гроза              6, -, -, 8
	7 - ночь | ясно        |                    7, 0, 0, -
	8 - ночь | малооблачно |                    8, 0, 1, -
	9 - ночь | пасмурно    |                    9, 0, 3, -
	10-      |             | дождь ливень       10,-, -, 4|5
	11-      |             | снег               11,-, -, 6|7
	*/
	$arMatrix = array(array(1,  '20-'), array(2,  '-1-'), array(3,  '-2-'), array(4,  '21-'), array(5,  '23-'), array(6,  '--8'), array(7,  '00-'), array(8,  '01-'), array(9,  '03-'), array(10, '--4'), array(10, '--5'), array(11, '--6'), array(11, '--7'));
	for($i=0; $i<count($arWeather); $i++)
	{
	    $nTod = $arWeather[$i]['FORECAST']['tod'];
	    if($nTod){
	    	$nTod = 2;
	    }
	    $nCloudiness = $arWeather[$i]['PHENOMENA']['cloudiness'];
	    $nPrecipitation = $arWeather[$i]['PHENOMENA']['precipitation'];
	    $arJoin = array($nTod, $nCloudiness, $nPrecipitation);
	    $nIcon = 1;
	    for($j=0; $j<count($arMatrix); $j++)
	    {
	    	$sCode = '';
	    	for($k=0; $k<3; $k++)
	    	{
		    	$sCode .= (substr($arMatrix[$j][1], $k, 1) == '-'?'-':$arJoin[$k]);
	    	}
	    	if($arMatrix[$j][1] == $sCode){
	    		$nIcon = $arMatrix[$j][0];
	    	}
	    }
		$arWeather[$i]['TEMPERATURE']['avg'] = $MOD['WEATHER']->Sign(ceil(($arWeather[$i]['TEMPERATURE']['max']+$arWeather[$i]['TEMPERATURE']['min'])/2));
		$arWeather[$i]['TEMPERATURE']['max'] = $MOD['WEATHER']->Sign($arWeather[$i]['TEMPERATURE']['max']);
		$arWeather[$i]['TEMPERATURE']['min'] = $MOD['WEATHER']->Sign($arWeather[$i]['TEMPERATURE']['min']);

		$arWeather[$i]['ICON'] = $nIcon;
		$arWeather[$i]['TOTD'] = $arTod[$arWeather[$i]['FORECAST']['tod']];
	}
	$MOD['CACHE']->SaveVar($arWeather);
}
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/weather/template/'.$sTemplate.'/template/index.php');
?>