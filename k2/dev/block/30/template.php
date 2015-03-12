<?
global $FILES;
?><div class="b-clients" id="client">
	<h2>Наши клиенты и партнеры</h2>
	<div class="box">
	<div class="block">
	<?
    $arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('SECTION_BLOCK' => $CURRENT['SECTION_BLOCK']['ID'], 'ACTIVE' => 1), array('SORT' => 'ASC'));
	for($i=0; $i<count($arList); $i++)
	{
    	if($bFirst){
    	 	?><div class="block"><?
    	 	$bFirst = false;
    	}
    	?><div class="item">
            <a<?
            if($arList[$i]['LINK']){
            	?> href="<?=$arList[$i]['LINK']?>" target="_blank"<?
            }
            ?>><img src="<?=$FILES[$arList[$i]['PHOTO']]?>" alt="content" /></a>
        </div><?
        if(!is_float(($i+1)/5)){
        	?><div class="clear"></div></div><?
        	$bFirst = true;
        }
	}
    ?></div></div>
</div>
<script>
$(function(){
   $('#client .box').cycle({
	    fx:'scrollLeft',
	    delay:1000,
		timeout:5000
	});
});
</script>