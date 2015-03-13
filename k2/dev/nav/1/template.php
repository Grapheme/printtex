<div class="pagination"><?
for($i=0; $i<$arSetting['PAGES']; $i++)
{
	if($arList[$i]['CURRENT']){
		?><span><?=($i+1)?></span><?
	}else{
		?> <a href="<?=$arList[$i]['URL']?>"><?=($i+1)?></a> <?
	}
}
?></div><?