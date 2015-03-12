<div class="section-content">
	<ul class="nice-list"><?
	for($i=0; $i<count($arList); $i++)
	{
		?><li>
			<div class="left"><?=$arList[$i]['CODE']?></div>
			<div class="right"><?=$arList[$i]['VALUE']?></div>
			<div class="clearer">&nbsp;</div>
		</li><?
	}
	?></ul>
</div>