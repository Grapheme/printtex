<?
$LIB['EVENT']->Add('BEFORE_EDIT_BLOCK_ELEMENT', 'beforeEditBlockElement');
function beforeEditBlockElement($arPar)
{
	$arPar['MATRIX'] = serialize(array($arPar['P'], $arPar['N']));
}
?>