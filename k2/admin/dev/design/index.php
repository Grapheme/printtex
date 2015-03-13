<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Функциональные блоки', '/dev/block/'), array('Компоненты', '/dev/component/'), array('Шаблоны навигации', '/dev/nav/'), array('Макеты дизайна', '/dev/design/', 1), array('Поля', '/dev/field/')));
?>
<div class="content">
	<h1>Список макетов</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить макет</a></td>
        </tr>
    </table>
   	<table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%">ID</th>
	   		<th>Название</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
	   	$arList = $LIB['DESIGN']->Rows();
	   	for($i=0; $i<count($arList); $i++)
		{
			?><tr class="<?
				if($i%2){
					?> odd<?
				}
				?>" goto="edit.php?id=<?=$arList[$i]['ID']?>">
				<td align="center"><?=$arList[$i]['ID']?></td>
				<td><a href="edit.php?id=<?=$arList[$i]['ID']?>"><?=html($arList[$i]['NAME'])?></a></td>
				<td align="center"><a href="delete.php?id=<?=$arList[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arList[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
    	if(!$i){
        	?><tr class="noblick empty">
        		<td colspan="3" align="center" height="100">Нет данных</td>
			</tr><?
    	}
		?></tbody>
	</table>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>