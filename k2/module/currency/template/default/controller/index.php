<?
if($MOD['CACHE']->Get(86400, 'currency'.date('z').serialize($MOD_SETTING['CURRENCY']))){
	$MOD['CURRENCY']->Update();
	$arRows = $MOD['CURRENCY']->Rows();
	for($i=0; $i<count($arRows); $i++)
	{
		if(in_array($arRows[$i]['CODE'], $MOD_SETTING['CURRENCY']['CODE'])){
			$arList[] = $arRows[$i];
		}
	}
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/currency/template/'.$sTemplate.'/template/index.php');
	$MOD['CACHE']->Save();
}
?>