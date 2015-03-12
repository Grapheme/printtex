<?
function randSplash()
{
	$arColor = array('red', 'pink', 'blue', 'green', 'yellow', 'turquoise');
	echo $arColor[array_rand($arColor)];
}
?>