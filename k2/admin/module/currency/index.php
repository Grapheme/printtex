<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'CURRENCY');
tab(array(array('Модули', '/module/'), array('Курс валют', '/module/currency/', 1)));
tab_(array(array('Настройки', '/module/currency/', 1), array('Шаблоны', '/module/currency/template/')));

$arModule = $LIB['MODULE']->ID('CURRENCY');
if($_POST){
	if($sError){
		$LIB['MODULE']->Error = $sError;
	}else{
		if($LIB['MODULE']->Edit('CURRENCY', $_POST)){
			Redirect('/k2/admin/module/currency/?complite=1');
		}else{
			echo $LIB['MODULE']->Error;
		}
	}
}else{
	$_POST = $arModule;
	$_POST['TEMPLATE'] = $LIB['FILE']->Read('/k2/module/currency/template.php');
}

?><div class="content">
    <form action="/k2/admin/module/currency/" method="post" enctype="multipart/form-data" class="form">
    	<?formError($LIB['MODULE']->Error)?>
    	<input type="hidden" name="ACTIVE" value="0">
        <div class="item">
			<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
			if($_POST['ACTIVE']){
				?> checked="checked"<?
			}
			?>>Активность</label>
		</div>
        <div class="item">
			<div class="name">Валюта для отображения<span class="star">*</span></div>
			<div class="field"><select name="SETTING[CODE][]" multiple="multiple" size="10"><?
			    $arCurrency = array(
			    'AUD' => 'Австралийский доллар',
			    'AZN' => 'Азербайджанский манат',
			    'GBP' => 'Фунт стерлингов Соединенного королевства',
			    'AMD' => 'Армянских драмов',
			    'BYR' => 'Белорусских рублей',
			    'BGN' => 'Болгарский лев',
			    'BRL' => 'Бразильский реал',
			    'HUF' => 'Венгерских форинтов',
			    'DKK' => 'Датских крон',
			    'USD' => 'Доллар США',
			    'EUR' => 'Евро',
			    'INR' => 'Индийских рупий',
			    'KZT' => 'Казахских тенге',
			    'CAD' => 'Канадский доллар',
			    'KGS' => 'Киргизских сомов',
			    'CNY' => 'Китайских юаней',
			    'LVL' => 'Латвийский лат',
			    'LTL' => 'Литовский лит',
			    'MDL' => 'Молдавских леев',
			    'NOK' => 'Норвежских крон',
			    'PLN' => 'Польский злотый',
			    'RON' => 'Новых румынских леев',
			    'XDR' => 'СДР (специальные права заимствования)',
			    'SGD' => 'Сингапурский доллар',
			    'TJS' => 'Таджикских сомони',
			    'TRY' => 'Турецкая лира',
			    'TMT' => 'Новый туркменский манат',
			    'UZS' => 'Узбекских сумов',
			    'UAH' => 'Украинских гривен',
			    'CZK' => 'Чешских крон',
			    'SEK' => 'Шведских крон',
			    'CHF' => 'Швейцарский франк',
			    'EEK' => 'Эстонских крон',
			    'ZAR' => 'Южноафриканских рэндов',
			    'KRW' => 'Вон Республики Корея',
			    'JPY' => 'Японских иен'
				);
				foreach($arCurrency as $sKey=>$sText)
				{
					?><option value="<?=$sKey?>"<?
					if($_POST['SETTING']['CODE'] && in_array($sKey, $_POST['SETTING']['CODE'])){
						?> selected="selected"<?
					}
					?>><?=$sText?></option><?
				}
			?></select></div>
		</div>
        <div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>