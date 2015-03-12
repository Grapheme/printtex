<?
global $MATRIX;

$arTiraz = $LIB['SELECT']->ID(6);
$arColor = $LIB['SELECT']->ID(7);

?><div class="b-prices">
	<table>
    	<tr>
    		<th>Тираж</th>
    		<?
    		for($j=0; $j<count($arColor['OPTION']); $j++)
    		{
    			?><th rel="<?=($j+1)?>"><?=$arColor['OPTION'][$j]['NAME']?> цв.</th><?
    		}
    		?></tr><?
    		for($j=0; $j<count($arTiraz['OPTION']); $j++)
    		{
    			?><tr>
    				<td rel="<?=$arTiraz['OPTION'][$j]['NAME']?>"><?=$arTiraz['OPTION'][$j]['NAME']?></td><?
    				for($k=0; $k<count($arColor['OPTION']); $k++)
    				{
    					?><td t="<?=$arTiraz['OPTION'][$j]['NAME']?>" c="<?=$arColor['OPTION'][$k]['NAME']?>"><?=$MATRIX[0][$arTiraz['OPTION'][$j]['NAME']][$arColor['OPTION'][$k]['NAME']]?></td><?
    				}
    				?>
    			</tr><?
    		}
    		?>
    </table>
    <div class="shadow"></div>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/26/form.php');
?><br><br><?