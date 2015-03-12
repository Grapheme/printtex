<?
$arElm = $LIB['BLOCK_ELEMENT']->ID($CURRENT['ELEMENT']['ID'], $CURRENT['SECTION_BLOCK']['ID']);
$LIB['TOOL']->Delayed('TITLE', $arElm['NAME']);
$LIB['NAV']->BackAdd(2, array($arElm['NAME'], $arElm['URL']));

echo $arElm['TEXT_FULL'];