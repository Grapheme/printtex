<?
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/function.php');

$arList = $LIB['BLOCK_ELEMENT']->Rows(33, array('ACTIVE' => 1), array('SORT' => 'ASC'), array('MATRIX'));
for($i=0; $i<count($arList); $i++)
{
	$MATRIX = unserialize($arList[$i]['MATRIX']);
}
$nOne = str_replace(',', '.', $MATRIX[0][$_POST['TIRAZ']][$_POST['COLOR']]);

if($_POST['FORMAT'] == 'Меньше А4'){
	$nOne = $nOne-(($nOne/100)*15);
}
if($_POST['FORMAT'] == 'А3'){
	$nOne = $nOne+(($nOne/100)*25);
}



if($_POST['EFFECT'] == 'Вытравка'){
	$nOne = $nOne+(($nOne/100)*30);
}else
if($_POST['EFFECT']){
	$nOne += $MATRIX[1][$_POST['TIRAZ']];
}

$nOne = round($nOne, 2);

?>['<?=$nOne?>', '<?=(float)($nOne*$_POST['TIRAZ'])?>']<?
