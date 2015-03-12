<?
global $FILES;
?><div class="b-services-list">
	<?
	$arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('SECTION_BLOCK' => $CURRENT['SECTION_BLOCK']['ID'], 'ACTIVE' => 1), array('SORT' => 'ASC'));
	for($i=0; $i<count($arList); $i++)
	{
		?><div class="item <?=$arList[$i]['CLASS']?>">
			<div class="image">
				<a href="<?=$arList[$i]['URL']?>"><img src="<?=$FILES[$arList[$i]['PHOTO']]?>" alt="textile" /></a>
			</div>
			<div class="description">
				<h2><a href="<?=$arList[$i]['URL']?>"><?=$arList[$i]['NAME']?></a></h2>
				<p><?=nl2br($arList[$i]['TEXT'])?></p>
			</div>
		</div><?
	}
	?>
</div>