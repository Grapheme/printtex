<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/header.php');

if($_CATEGORY){
	if($arCategory = $LIB['COMPONENT_CATEGORY']->Back(array('COMPONENT' => $_COMPONENT, 'ID' => $_CATEGORY))){
		$arNav[] = array('Компонент', '/system/component/?field='.$_FIELD.'&component='.$_COMPONENT.'&collection='.$_COLLECTION);
		for($i=0; $i<count($arCategory); $i++)
		{
			$arNav[] = array($arCategory[$i]['NAME'], '/system/component/?field='.$_FIELD.'&component='.$_COMPONENT.'&collection='.$_COLLECTION.'&category='.$arCategory[$i]['ID']);
		}
		navBack($arNav);
	}
}

$arField['ID'] = array('NAME' => 'ID', 'FORMAT' => '', 'ALIGN' => 'center', 'ACTIVE' => 1);
$arRows = $DB->Rows("SHOW COLUMNS FROM `k2_component".$_COMPONENT."`");
$arField = fieldFormat('k2_component'.$_COMPONENT, $arField);

$QB = new QueryBuilder;
$QB->From('k2_component'.$_COMPONENT.' AS B');
$QB->Where('B.COLLECTION = ?', $_COLLECTION);
$QB->AndWhere('B.CATEGORY = ?', $_CATEGORY);
$QB->Num = true;

$nLimit = 20;
$arSort = array('FIELD' => 'ID', 'METHOD' => 'asc');
if($arRows = userSettingSession(true)){
	if($arField[$arRows['PAGE_SORT']['FIELD']]){
		$arSort = $arRows['PAGE_SORT'];
	}
	if($arRows['PAGE_SIZE'] > 1){
		$nLimit = $arRows['PAGE_SIZE'];
	}
}

$QB->OrderBy('B.'.$arSort['FIELD'].' '.$arSort['METHOD']);

$nOffset = 0;
if($_PAGE>1){
	$nOffset = $_PAGE*$nLimit-$nLimit;
}

$QB->Limit($nOffset.', '.$nLimit);

foreach($arField as $sKey => $sValue)
{	$arField[$sKey]['ACTIVE'] = 1;
}

$arTableHead[] = array('HTML' => '<th width="1%" class="first"><input type="checkbox" title="Отметить поля" onclick="table.check.all(this, \'.table-body\')"></th>');
$arTableHead = fieldTableHead('B', $QB, $arField, $arSort, $arTableHead);
$arTableHead[] = array('NAME' => 'Действие');

$QB->Select('B.ID, B.ACTIVE');
#p($QB->Build());
#p($DB->Rows($QB->Build()));

$arList = $DB->Rows($QB->Build());
$arCount = $DB->Row("SELECT FOUND_ROWS()");
$sURI = '?field='.$_FIELD.'&component='.$_COMPONENT.'&collection='.$_COLLECTION.'&category='.$_CATEGORY;
$nPage = $_PAGE;
$sNav = navPage($arCount['FOUND_ROWS()'], $nLimit, $sURI.'&');
if($nPage > $_PAGE){
	Redirect('/k2/admin/system/component/'.$sURI);
}

$arUserLogin = userAllLogin();
?><table width="100%" class="nav">
	<tr>
		<td><?=$sNav?></td>
		<td align="right"><?
		if($arComponent['CATEGORY'] && !$arCount['FOUND_ROWS()']){
			?><a href="../category/add.php<?=$sURI?>" class="button">Добавить категорию</a><?
		}
		?><a href="../element/add.php<?=$sURI?>" class="button">Добавить элемент</a></td>
		</tr>
</table>
<form method="post" id="form">
	<input type="hidden" name="field" value="<?=$_FIELD?>">
	<input type="hidden" name="component" value="<?=$_COMPONENT?>">
	<input type="hidden" name="collection" value="<?=$_COLLECTION?>">
	<input type="hidden" name="category" value="<?=$_CATEGORY?>">
	<table width="100%" class="table">
		<tr><?=tableHead($arTableHead, $arSort);?></tr>
		<tbody class="table-body"><?
		for($i=0; $i<count($arList); $i++)
		{
			?><tr class="<?
			if($i%2){
				?> odd<?
			}
			if(!$arList[$i]['ACTIVE']){
				?> passive<?
			}
			?>" goto="edit.php<?=$sURL?>&id=<?=$arList[$i]['ID']?>">
				<td><input type="checkbox" name="ID[]" value="<?=$arList[$i]['ID']?>"></td><?
				tableBody(array(
				'CONTENT' => $arList[$i],
				'FIELD' => $arField,
				'USER_LOGIN' => $arUserLogin,
				'PREVIEW' => $arSettingView['PREVIEW']
				));
				?>
				<td align="center" class="action"><a href="delete.php<?=$sURL?>&id=<?=$arList[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php<?=$sURL?>&id=<?=$arList[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
		if(!$i){
			?><tr class="noblick empty">
			<td colspan="<?=count($arTableHead)+2?>" align="center" height="100">Нет данных</td>
			</tr><?
		}
		?>
		</tbody>
	</table>
	<table width="100%" class="nav">
	<tr>
	   	<td>
	       	<div class="navPage"><?=$sNav?></div>
	       </td>
	   </tr>
	</table><?
	if($i){
		?><table width="100%" class="select">
	    	<tr>
	        	<td>С отмеченными<select id="action" disabled><option value="">Выбрать действие</option><option value="delete">Удалить</option></select>
	        	<script>
	            $('#action').change(function(){
	            	val = $(this).val();
	            	if(!val){
	            		return false;
	            	}
	            	data = $('#form').serialize();
	                if(data.length){
	                	if(val == 'delete'){
	                		$.prompt(this, {'href':'/k2/admin/system/component/element/delete.php<?=$sURI?>', 'yes':'return actionDelete(1)', 'no':'return actionDelete(0)'});
	                	}
	                }
	            });
	            $('#form input').change(function(){
	            	$('#action')[$('.table-body input:checkbox:checked').size()?'removeAttr':'attr']('disabled', 'disabled');
	            });
	        	</script>
	        	</td>
	            <td align="right">На странице <select id="sizePage" url="/k2/admin/system/component/element/<?=$sURI?>&"><?
	            $arSize = array(10, 20, 50, 100);
	            for($i=0; $i<count($arSize); $i++)
	            {
	            	?><option<?
	            	if($nLimit == $arSize[$i]){
	            		?> selected<?
	            	}
	            	?>><?=$arSize[$i]?></option><?
	            }
	            ?></select></td>
	        </tr>
	    </table><?
	}
	?>
</form><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/footer.php');
?>