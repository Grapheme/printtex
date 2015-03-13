<?
global $FILES;
?><div class="b-portfolio"><?
    $arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('SECTION_BLOCK' => $CURRENT['SECTION_BLOCK']['ID'], 'ACTIVE' => 1), array('SORT' => 'ASC'), false, array('SIZE' => 16));
	for($i=0; $i<count($arList); $i++)
	{
    	$arPhotos = toArray($arList[$i]['PHOTO']);
    	$arPreview = $LIB['PHOTO']->Preview($arPhotos[0], array('WIDTH' => 190, 'HEIGHT' => 120, 'FIX' => 1));
    	?><div class="item">
			<div class="image">
				<a href="<?=$arList[$i]['URL']?>"><img src="<?=$arPreview['PATH']?>" alt="content" width="190" height="120"/></a>
			</div>
			<div class="link">
				<a href="<?=$arList[$i]['URL']?>"><?=$arList[$i]['NAME']?></a>
			</div>
		</div><?
	}
    ?>
</div><?
$LIB['NAV']->Page(1);
?>