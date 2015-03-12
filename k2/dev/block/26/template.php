<div class="b-about"><?
$arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('SECTION_BLOCK' => $CURRENT['SECTION_BLOCK']['ID'], 'ACTIVE' => 1), array('SORT' => 'ASC'), array('TEXT'));
for($i=0; $i<count($arList); $i++)
{
	echo $arList[$i]['TEXT'];
}
?></div><?
templateInclude('form.php');
?>