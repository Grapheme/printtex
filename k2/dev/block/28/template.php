<?
global $FILES;
?><div class="b-section-list">
	<?
	$arCategory = $LIB['BLOCK_CATEGORY']->Rows($CURRENT['BLOCK']['ID'], array('SECTION_BLOCK' => $CURRENT['SECTION_BLOCK']['ID'], 'ACTIVE' => 1), array('SORT' => 'ASC'));
	for($i=0; $i<count($arCategory); $i++)
	{
		?><a href="?c=<?=$arCategory[$i]['ID']?>" class="item<?
		if($_GET['c'] == $arCategory[$i]['ID']){			?> select<?
			$nActiveCategory = $arCategory[$i]['ID'];
			$LIB['NAV']->BackAdd(2, array($arCategory[$i]['NAME']));
		}
		?>">
            <img src="<?=$FILES[$arCategory[$i]['PHOTO']]?>" alt="content" />
            <p><?=$arCategory[$i]['NAME']?></p>
        </a><?
	}
	if(!$nActiveCategory && $arCategory[0]['ID']){		Redirect('?c='.$arCategory[0]['ID']);
	}
	?>
</div>
<div class="b-equipment-list">
    <?
    $arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('CATEGORY' => $nActiveCategory, 'ACTIVE' => 1), array('SORT' => 'ASC'));
	for($i=0; $i<count($arList); $i++)
	{
		?><div class="item" num="<?=$arList[$i]['ID']?>">
			<div class="image">
				<a href="#" class="link" num="<?=$arList[$i]['ID']?>"><img src="<?=$FILES[$arList[$i]['PHOTO']]?>" alt="content" /></a>
			</div>
			<div class="description">
				<h2><a href="#" class="link" num="<?=$arList[$i]['ID']?>"><?=$arList[$i]['NAME']?></a></h2>
				<div class="text"><div class="text-box"><?=$arList[$i]['TEXT']?></div></div>
			</div>
		</div><?
	}
    ?>
</div>