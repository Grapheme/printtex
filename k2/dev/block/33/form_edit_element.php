<div class="item">
	<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
	if($_POST['ACTIVE']){
		?> checked<?
	}
	?>>Активность</label>
</div><?
if(!$_ID && !isset($_POST['SORT'])){	$_POST['SORT'] = 10;
	if($arContent = $DB->Row("SELECT SORT FROM `k2_block".$arSBlock['BLOCK']."` WHERE `SECTION_BLOCK` = '".$_SECTION_BLOCK."' ORDER BY `ID` DESC LIMIT 1")){		$_POST['SORT'] = $arContent['SORT'] + 10;
	}
}
?><div class="item">
	<div class="name">Сортировка<span class="star">*</span></div>
	<div class="field"><input type="text" name="SORT" value="<?=html($_POST['SORT'])?>"></div>
</div><?
$arField = array_merge($LIB['FIELD']->Rows('k2_block'.$arBlock['ID']), $LIB['FIELD_SEPARATOR']->Rows('k2_block'.$arBlock['ID']));
for($i=0; $i<count($arField); $i++)
{
	if(!$i){
		usort($arField, 'sortArray');
	}
	if(!$arField[$i]['FIELD']){
		?><div class="fieldGroup">
			<div class="title"><?=$arField[$i]['NAME']?></div>
		</div><?
	}else{
		if($arField[$i]['FIELD'] == 'MATRIX' && $_GET['id']){
            $arTiraz = $LIB['SELECT']->ID(6);
            $arColor = $LIB['SELECT']->ID(7);

            $arUnserialize = unserialize($_POST['MATRIX']);
            $_POST['P'] = $arUnserialize[0];
            $_POST['N'] = $arUnserialize[1];

            ?><table width="100%" class="table">
            	<tr>
            		<th class="first" width="1%">Тираж</th>
            		<?
            		for($j=0; $j<count($arColor['OPTION']); $j++)
            		{            			?><th><?=$arColor['OPTION'][$j]['NAME']?> цв.</th><?
            		}
            		?><th>Надбавка</th>
            		</tr><?
            		for($j=0; $j<count($arTiraz['OPTION']); $j++)
            		{
            			?><tr>
            				<th><?=$arTiraz['OPTION'][$j]['NAME']?></th><?
            				for($k=0; $k<count($arColor['OPTION']); $k++)
            				{            					?><td><input type="text" class="e" name="P[<?=$arTiraz['OPTION'][$j]['NAME']?>][<?=$arColor['OPTION'][$k]['NAME']?>]" value="<?=$_POST['P'][$arTiraz['OPTION'][$j]['NAME']][$arColor['OPTION'][$k]['NAME']]?>" style="width:100%; text-align:center"></td><?
            				}
            				?><td><input type="text" class="e" name="N[<?=$arTiraz['OPTION'][$j]['NAME']?>]" value="<?=$_POST['N'][$arTiraz['OPTION'][$j]['NAME']]?>" style="width:100%; text-align:center"></td>
            			</tr><?
            		}
            		?>
            </table><?
		}
		echo $LIB['FORM']->Element($arField[$i]['ID'], '<div class="item"><div class="name">%NAME%</div><div class="field">%FIELD%</div></div>');
	}
}
?>