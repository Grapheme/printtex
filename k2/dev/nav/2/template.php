<?
for($i=0, $c = count($arList); $i<$c; $i++)
{
	if($i){		?> &raquo; <?
	}
	if($c == $i+1){
		echo $arList[$i]['NAME'];
	}else{
		?><a href="<?=$arList[$i]['URL']?>"><?=$arList[$i]['NAME']?></a><?
	}
}
?>