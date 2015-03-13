<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Функциональные блоки', '/dev/block/'), array('Компоненты', '/dev/component/'), array('Шаблоны навигации', '/dev/nav/'), array('Макеты дизайна', '/dev/design/'), array('Поля', '/dev/field/', 1)));
tab_(array(array('Для сайта', '/dev/field/site/'), array('Для разделов', '/dev/field/section/', 1), array('Для пользователей', '/dev/field/user/')));
?>
<div class="content">
	<h1>Список полей</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="#" onclick="return $.layer({get:'/k2/admin/dev/field/separator/add.php?table=k2_section', title:'Добавить разделитель', w:397}, function(){k2.group.add()})" class="button">Добавить разделитель</a><a href="add.php" class="button">Добавить поле</a></td>
        </tr>
    </table>
   	<table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%"><div class="icon moveWhite" title="Сортировка"></div></th>
	   		<th width="1%">ID</th>
	   		<th width="50%">Описание</th>
	   		<th>Поле</th>
	   		<th>Тип</th>
	   		<th>Обязательное</th>
	   		<th>Действие</th>
	   	</tr>
    	<tbody class="sf-body"><?
    	if($arField = array_merge($LIB['FIELD']->Rows('k2_section'), $LIB['FIELD_SEPARATOR']->Rows('k2_section'))){
        	usort($arField, 'sortArray');
        	for($i=0; $i<count($arField); $i++)
			{
				if(!$arField[$i]['FIELD']){
                ?><tr field="<?=$arField[$i]['ID']?>" class="group">
						<td class="sf-td"><div class="icon move"></div></td>
						<td></td>
						<td colspan="4"><?=html($arField[$i]['NAME'])?></td>
						<td align="center"><a href="/k2/admin/dev/field/separator/delete.php?id=<?=$arField[$i]['ID']?>&back=<?=base64_encode($_SERVER['REQUEST_URI'])?>" onclick="return $.prompt(this)" class="icon deleteWhite" title="Удалить разделитель"></a><a href="#" onclick="$.layer({'get':'/k2/admin/dev/field/separator/edit.php?id=<?=$arField[$i]['ID']?>', 'title':'Редактировать разделитель', w:397}, function(){k2.group.edit()})" class="icon editWhite" title="Редактировать разделитель"></a></td>
					</tr><?
					continue;
				}
				?><tr class="<?
				if($i%2){
					?> odd<?
				}
				?>" goto="edit.php?id=<?=$arField[$i]['ID']?>" field="<?=$arField[$i]['ID']?>">
					<td class="sf-td"><div class="icon move"></div></td>

                    <td align="center"><?=$arField[$i]['ID']?></td>
					<td><a href="edit.php?id=<?=$arField[$i]['ID']?>"><?=$arField[$i]['NAME']?></a></td>
					<td><?=$arField[$i]['FIELD']?></td>
					<td><?=fieldType($arField[$i]['TYPE'])?></td>
					<td align="center"><?=($arField[$i]['REQUIRED']?'Да':'Нет')?></td>
					<td align="center"><a href="delete.php?id=<?=$arField[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arField[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
				</tr><?
			}
    	}else{
        	?><tr class="noblick empty">
        		<td colspan="7" align="center" height="100">Нет данных</td>
			</tr><?
    	}
		?>
		</tbody>
	</table>
	<script type="text/javascript">table.sort(0)</script>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>