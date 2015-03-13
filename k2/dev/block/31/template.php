<?
global $FILES;
?><div class="b-guild-slider">
    <div id="slides"><?
    $arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('SECTION_BLOCK' => $CURRENT['SECTION_BLOCK']['ID'], 'ACTIVE' => 1), array('SORT' => 'ASC'));
	for($i=0; $i<count($arList); $i++)
	{
    	?><img src="<?=$FILES[$arList[$i]['PHOTO']]?>" width="676" height="400"><?
	}
    ?>
    </div>
</div>
