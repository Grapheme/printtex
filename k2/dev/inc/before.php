<?
$LIB['TOOL']->Delayed('TITLE', $CURRENT['SECTION']['NAME']);
$LIB['TOOL']->Delayed('KEYWORD', '');
$LIB['TOOL']->Delayed('DESCRIPTION', '');

$arRows = $DB->Rows("SELECT * FROM `k2_file`");
for($i=0; $i<count($arRows); $i++)
{
	$FILES[$arRows[$i]['ID']] = '/files/'.$arRows[$i]['PATH'];
}
$arList = $LIB['BLOCK_ELEMENT']->Rows(33, array('ACTIVE' => 1), array('SORT' => 'ASC'), array('MATRIX'));
for($i=0; $i<count($arList); $i++)
{
	$MATRIX = unserialize($arList[$i]['MATRIX']);
}
?>