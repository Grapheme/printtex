<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Функциональные блоки', '/dev/block/'), array('Компоненты', '/dev/component/', 1), array('Шаблоны навигации', '/dev/nav/'), array('Макеты дизайна', '/dev/design/'), array('Поля', '/dev/field/')));
?>
<div class="content">
	<h1>Список компонентов</h1><?
	$arGroup = $LIB['COMPONENT_GROUP']->Rows();
	?>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><?
            if($arGroup){
				?><a href="import/" class="button">Импортировать</a><a href="#" onclick="return $.layer({get:'/k2/admin/dev/group/add.php?object=1', title:'Добавить группу', w:397}, function(){k2.group.add()})" class="button">Добавить группу</a><a href="add.php" class="button">Добавить компонент</a><?
			}else{
				?><a href="#" onclick="return $.layer({get:'/k2/admin/dev/group/add.php?object=1', title:'Добавить группу', w:397}, function(){k2.group.add()})" class="button">Добавить группу</a><?
			}
            ?></td>
        </tr>
    </table>
   	<table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%">ID</th>
	   		<th>Название</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
		for($i=0; $i<count($arGroup); $i++)
		{
			$arBlock = $LIB['COMPONENT']->Rows($arGroup[$i]['ID']);
			?><tr class="group">
				<td></td>
				<td><?=$arGroup[$i]['NAME']?></td>
				<td align="center"><?
				if($arBlock){
	            	?><div class="icon empty"></div><?
				}else{
					?><a href="/k2/admin/dev/group/delete.php?id=<?=$arGroup[$i]['ID']?>&object=1" onclick="return $.prompt(this)" class="icon deleteWhite" title="Удалить группу"></a><?
				}
				?><a href="#" onclick="$.layer({'get':'/k2/admin/dev/group/edit.php?id=<?=$arGroup[$i]['ID']?>&object=1', 'title':'Редактировать группу', w:398}, function(){k2.group.edit()})" class="icon editWhite" title="Редактировать группу"></a></td>
			</tr><?
			for($n=0; $n<count($arBlock); $n++)
			{
				?><tr class="<?
				if($n%2){
					?> odd<?
				}
				?>" goto="edit.php?id=<?=$arBlock[$n]['ID']?>">
					<td><?=$arBlock[$n]['ID']?></td>
					<td align="left"><a href="edit.php?id=<?=$arBlock[$n]['ID']?>"><?=html($arBlock[$n]['NAME'])?></a></td>
					<td align="center"><a href="delete.php?id=<?=$arBlock[$n]['ID']?>&object=1" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arBlock[$n]['ID']?>" class="icon edit" title="Редактировать"></a></td>
				</tr><?
			}
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