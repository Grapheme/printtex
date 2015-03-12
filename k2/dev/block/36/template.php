<?
global $FILES;
?><div class="b-slider">
	<div class="box"><?
    $arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('ACTIVE' => 1), array('SORT' => 'ASC'));
	for($i=0; $i<count($arList); $i++)
	{
   		?><a href="<?=$arList[$i]['LINK']?>" style="background:#fff url(<?=$FILES[$arList[$i]['PHOTO']]?>) center center no-repeat"></a><?
	}
    ?></div>
    <!--<div class="nav"></div>-->
</div>
<script>
$(function(){
	$('.b-slider .box').cycle({
	    fx:'fade',
	    delay:1000,
		pager:'.b-slider .nav',
		timeout:5000
	});
});
 </script>