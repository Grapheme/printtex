<?
$arElm = $LIB['BLOCK_ELEMENT']->ID($CURRENT['ELEMENT']['ID'], $CURRENT['SECTION_BLOCK']['ID']);
$LIB['TOOL']->Delayed('TITLE', $arElm['NAME']);
$LIB['NAV']->BackAdd(2, array($arElm['NAME'], $arElm['URL']));
$arPhotos = toArray($arElm['PHOTO']);
$n = count($arPhotos);
?>
<div id="pg_scrollWrapper" class="pg_scrollWrapper">
	<div id="slider" class="slider"></div>
</div>
<div id="pg_container" class="pg_container">
	<ul id="pg_photos" class="pg_photos">
		<?
		for($i=0; $i<$n; $i++)
		{
	    	$arFull = $LIB['PHOTO']->Preview($arPhotos[$i], array('WIDTH' => 1000, 'HEIGHT' => 448));
	    	?><li><a href="#<?=($i+1)?>"><img src="<?=$arFull['PATH']?>"></a></li>
	    	<?
		}
		?>
	</ul>
</div>
<div class="thumbnailSlider" id="thumbnailSlider">
	<ul class="ts_container">
		<?
		for($i=0; $i<$n; $i++)
		{
	    	?><li><a href="#<?=($i+1)?>"></a></li>
	    	<?
		}
		?>
		<li class="ts_thumbnails">
			<div class="ts_preview_wrapper">
				<ul class="ts_preview">
					<?
					for($i=0; $i<$n; $i++)
					{
				    	$arPreview = $LIB['PHOTO']->Preview($arPhotos[$i], array('WIDTH' => 75, 'HEIGHT' => 75, 'FIX' => 1));
				    	?><li><img src="<?=$arPreview['PATH']?>"></li>
				    	<?
					}
					?>
				</ul>
			</div>
			<span></span>
		</li>
	</ul>
</div>