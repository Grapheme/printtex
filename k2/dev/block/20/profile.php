<?
$LIB['TOOL']->Delayed('TITLE', 'Профиль');
$LIB['NAV']->BackAdd(2, array('Профиль'));
if($_POST){
	if($LIB['USER']->Edit($USER['ID'], $_POST)){
   		$arUser = $LIB['USER']->ID($USER['ID']);
		Redirect('?action=profile&complite=1');
	}else{
		$sMessage = '<div class="error">'.$LIB['USER']->Error.'</div>';
	}
}else{
	$_POST = $USER;
}
if($_GET['complite']){
	$sMessage = '<div class="success">Профиль сохранен</div>';
}
?><form action="<?=$CURRENT['PATH']?>?action=profile" method="post" enctype="multipart/form-data" id="form">
	<table class="table">
		<tr>
			<td width="100"></td>
			<td><?=$sMessage?></td>
		</tr>
		<tr>
			<td class="r">Новый пароль</td>
			<td class="field"><input type="password" name="PASSWORD" value="<?=html($_POST['PASSWORD'])?>"></td>
		</tr>
		<tr>
			<td class="r">Повтор пароля</td>
			<td class="field"><input type="password" name="PASSWORD_RETRY" value="<?=html($_POST['PASSWORD_RETRY'])?>"></td>
		</tr>
		<tr>
			<td class="r">Эл. почта<span class="star">*</span></td>
			<td class="field"><input type="text" name="EMAIL" value="<?=html($_POST['EMAIL'])?>"></td>
		</tr><?
		$arField = $LIB['FIELD']->Rows(3);
		for($i=0, $n=1; $i<count($arField); $i++)
		{
			echo $LIB['FORM']->Element($arField[$i]['ID'], '<tr><td class="r">%NAME%</td><td>%FIELD%</td></tr>');
			$n++;
		}
		?><tr>
			<td></td>
			<td class="note"><span class="star">*</span> &mdash; поля, обязательные для заполнения</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Отправить"></td>
		</tr>
	</table>
</form><?
?>