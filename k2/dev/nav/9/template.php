<ul class="footer-menu"><?
	for($i=0, $c=count($arList); $i<$c; $i++)
   	{
   		if($arList[$i]['ID'] == 1){
			$arList[$i]['URL'] = '/';
		}
		?><li<?
		if($arList[$i]['CURRENT']){
			?> class="select"<?
		}
		?>><a href="<?=$arList[$i]['URL']?>"><?=$arList[$i]['NAME']?></a></li><?
   	}
	?>
</ul>