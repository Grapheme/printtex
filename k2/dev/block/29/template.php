<?
global $FILES;
?><div class="b-features">
    <h2>Наши возможности</h2>
    <?
    $arCategory = $LIB['BLOCK_CATEGORY']->Rows(28, array('ACTIVE' => 1), array('SORT' => 'ASC'));
	for($i=0; $i<count($arCategory); $i++)
	{    	?><div class="item">
	        <div class="image"><a href="/ob/?c=<?=$arCategory[$i]['ID']?>"><img src="<?=$FILES[$arCategory[$i]['PHOTO_']]?>" alt="content" /></a></div>
	        <?=$arCategory[$i]['TEXT']?>
	    </div><?
	}
    ?>
</div>