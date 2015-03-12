<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SEO');
tab(array(array('Модули', '/module/'), array('SEO', '/module/seo/', 1)));

?><div class="content">
	<h1>Список страниц</h1>
	<table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить страницу</a></td>
        </tr>
    </table>
	<table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%">ID</th>
	   		<th>Путь</th>
	   		<th>Заголовок</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
	   	$arRows = $MOD['SEO']->Rows();
		for($i=0; $i<count($arRows); $i++)
		{
			?><tr goto="edit.php?id=<?=$arRows[$i]['ID']?>">
				<td><?=$arRows[$i]['ID']?></td>
				<td><a href="edit.php?id=<?=$arRows[$i]['ID']?>"><?=html($arRows[$i]['PAGE'])?></a></td>
				<td><?=$arRows[$i]['TITLE']?></td>
				<td align="center"><a href="delete.php?id=<?=$arRows[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arRows[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
    	if(!$i){
        	?><tr class="noblick empty">
        		<td colspan="4" align="center" height="100">Нет данных</td>
			</tr><?
    	}
		?></tbody>
	</table>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>