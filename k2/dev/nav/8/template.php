<div id="menu-wrapper">
    <ul class="main-menu">
        <?
        for($i=0, $c=count($arList); $i<$c; $i++)
    	{
    		if($arList[$i]['ID'] == 1){
				$arList[$i]['URL'] = '/';
			}
			?><li<?
			if($arList[$i]['CURRENT']){
				?> class="select"<?
			}
			?>><a href="<?=$arList[$i]['URL']?>"><?=$arList[$i]['NAME']?></a><?
			// � ���� ��� ����� ���������� �� ������ ����������, �� � ������ ���������/���������
			$arSection = array();
			if($arList[$i]['ID'] == 65){
            	$arSection = $LIB['BLOCK_ELEMENT']->Rows(27, array('ACTIVE' => 1), array('SORT' => 'ASC'));
			}elseif($arList[$i]['ID'] == 66){
            	$arCategory = $LIB['BLOCK_CATEGORY']->Rows(28, array('ACTIVE' => 1), array('SORT' => 'ASC'));
				for($j=0; $j<count($arCategory); $j++)
				{
					$arSection[] = array('NAME' => $arCategory[$j]['NAME'], 'URL' => $arList[$i]['URL'].'?c='.$arCategory[$j]['ID']);
				}
			}else{
			}
	        if($arSection){
	        	?><ul><?
	        	for($j=0; $j<count($arSection); $j++)
	        	{
	        	}
	        	?></ul><?
	        }
			?></li><?
    	}
        ?>
    </ul>
</div>