<link rel="stylesheet" type="text/css" href="/k2/module/weather/template/<?=$sTemplate?>/static/style.css">
<div class="mod_weather">
	<div class="title">Погода г. <?=$MOD_SETTING['WEATHER']['CITY_NAME']?></div>
	<div class="now" style="background-position:0 -<?=($arWeather[0]['ICON']*64)?>px"><?=$arWeather[0]['TEMPERATURE']['avg']?>°</div>
	<ul><?
	for($i=1; $i<count($arWeather); $i++)
	{
		?><li>
			<div class="tod"><?=$arWeather[$i]['TOTD']?></div>
			<div class="icon" style="background-position:0 -<?=($arWeather[$i]['ICON']*16)?>px"><?=$arWeather[$i]['TEMPERATURE']['avg']?>°</div>
			<div class="clear"></div>
		</li><?
	}
	?></ul>
	<a href="http://www.gismeteo.ru" class="gismeteo">по данным www.gismeteo.ru</a>
</div>