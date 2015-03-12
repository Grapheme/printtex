<?
global $MATRIX;

$arTiraz = $LIB['SELECT']->ID(6);
$arColor = $LIB['SELECT']->ID(7);
$arFormat = $LIB['SELECT']->ID(8);
$arEffect = $LIB['SELECT']->ID(9);
?>
<div class="b-tarifs">
    <form action="post" class="calcForm">
	    <div class="header">
	        <h2>Тарифы на печать по текстилю</h2>
	    </div>
		<div class="body">
			<table>
				<tr><td>Тираж / шт.</td><td align="right"><select name="TIRAZ" class="calcTiraz"><?
				for($j=0; $j<count($arTiraz['OPTION']); $j++)
				{	            	if(!$j){	            		$nFirstTiraz = $arTiraz['OPTION'][$j]['NAME'];
	            	}
	            	?><option value="<?=$arTiraz['OPTION'][$j]['NAME']?>"><?=$arTiraz['OPTION'][$j]['NAME']?></option><?
				}
				?></select></td></tr>
				<tr><td>Количество цветов</td><td align="right"><select name="COLOR" class="calcColor"><?
				for($j=0; $j<count($arColor['OPTION']); $j++)
				{
	            	if(!$j){
	            		$nFirstColor = $arColor['OPTION'][$j]['NAME'];
	            	}
	            	?><option value="<?=$arColor['OPTION'][$j]['NAME']?>"><?=$arColor['OPTION'][$j]['NAME']?></option><?
				}
				?></select></td></tr>
				<tr><td>Формат</td><td align="right"><select name="FORMAT" class="calcFormat"><?
				for($j=0; $j<count($arFormat['OPTION']); $j++)
				{
	            	if(!$j){
	            		$nFirstFormat = $arFormat['OPTION'][$j]['NAME'];
	            	}
	            	?><option value="<?=$arFormat['OPTION'][$j]['NAME']?>"><?=$arFormat['OPTION'][$j]['NAME']?></option><?
				}
				?></select></td></tr>
				<tr><td>Эффект</td><td align="right"><select name="EFFECT" class="calcEffect"><option value="0">Отсутствует</option><?
				for($j=0; $j<count($arEffect['OPTION']); $j++)
				{
	            	if(!$j){
	            		$nFirstEffect = $arEffect['OPTION'][$j]['NAME'];
	            	}
	            	?><option value="<?=$arEffect['OPTION'][$j]['NAME']?>"><?=$arEffect['OPTION'][$j]['NAME']?></option><?
				}
				?></select></td></tr><?
				if($nFirstFormat == 'Меньше А4'){
					$MATRIX[0][$nFirstTiraz][$nFirstColor] = $MATRIX[0][$nFirstTiraz][$nFirstColor]-(($MATRIX[0][$nFirstTiraz][$nFirstColor]/100)*15);
				}
				if($nFirstFormat == 'А3'){
					$MATRIX[0][$nFirstTiraz][$nFirstColor] = $MATRIX[0][$nFirstTiraz][$nFirstColor]+(($MATRIX[0][$nFirstTiraz][$nFirstColor]/100)*25);
				}
				?>
				<tr><td>Стоимость за ед.</td><td align="right"><input type="text" readonly="readonly" class="calcOne" value="<?=$MATRIX[0][$nFirstTiraz][$nFirstColor]?> руб."/></td></tr>
				<tr><td>Стоимость тиража</td><td align="right"><input type="text" readonly="readonly" class="calcTotal" value="<?=$MATRIX[0][$nFirstTiraz][$nFirstColor]*$nFirstTiraz?> руб."/></td></tr>
			</table>
		</div>
		<div class="footer">
			<input type="submit" id="button-order" value="Оформить заказ" />
		</div>
	</form>
    <div class="shadow"></div>
</div>