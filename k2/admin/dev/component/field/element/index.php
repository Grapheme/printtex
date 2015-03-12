<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('TEMPLATE');
tab(array(array('Функциональные блоки', '/dev/block/'), array('Компоненты', '/dev/component/', 1), array('Шаблоны навигации', '/dev/nav/'), array('Макеты дизайна', '/dev/design/'), array('Поля', '/dev/field/')));
tab_(array(array('Настройки', '/dev/component/edit.php?id='.$_ID), array('Поля категорий', '/dev/component/field/category/?id='.$_ID), array('Поля элементов', '/dev/component/field/element/?id='.$_ID, 1)));
if(!$arComponent = $LIB['COMPONENT']->ID($_ID)){
	Redirect('/k2/admin/dev/component/');
}
?>
<div class="content">
	<h1>Список полей</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php?id=<?=$_ID?>" class="button">Добавить поле</a></td>
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
    	$arField = $LIB['FIELD']->Rows('k2_component'.$_ID);
       	for($i=0; $i<count($arField); $i++)
		{
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
     	if(!$i){        	?><tr class="noblick empty">
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