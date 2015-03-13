<?
global $FILES;
?><center><div class="b-services">
    <?
    $arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('ACTIVE' => 1), array('SORT' => 'ASC'));
	for($i=0; $i<count($arList); $i++)
	{
   		?><div class="item <?=$arList[$i]['CLASS']?>">
	        <a href="<?=$arList[$i]['LINK']?>">
	            <img src="<?=$FILES[$arList[$i]['PHOTO_']]?>" class="gray" />
	            <img src="<?=$FILES[$arList[$i]['PHOTO']]?>" class="color" />
	            <p><?=$arList[$i]['NAME']?></p>
	        </a>
	    </div><?
	}
    ?>
</div></center>