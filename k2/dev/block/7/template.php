<?
session_start();
$arField = $LIB['FIELD']->Rows(2, $CURRENT['BLOCK']['ID']);
if($_POST){
	if(isset($_SESSION['K2CAPTCHA']) && $_SESSION['K2CAPTCHA'] == $_POST['K2CAPTCHA']){
		$_POST['ACTIVE'] = 1;
		if($nID = $LIB['BLOCK_ELEMENT']->Add(22, $_POST)){
			$sBody  = "Новое сообщение с раздела '".$CURRENT['SECTION']['NAME']."'\n\n";
            for($i=0; $i<count($arField); $i++)
			{
				$sBody .= $arField[$i]['NAME'].": ".($_POST[$arField[$i]['FIELD']]?$_POST[$arField[$i]['FIELD']]:'-')."\n";
			}
	        $sBody .= "\n-------\nДата отправки: ".date('d-m-Y, H:i');
			sendMail($CURRENT['SITE']['EMAIL'], $CURRENT['SITE']['EMAIL'], $sBody);
			Redirect('?complite=1#form');
		}else{
			?><div class="error"><?=$LIB['BLOCK_ELEMENT']->Error?></div><?
		}
	}else{
		?><div class="error">Введены неправильные символы на картинки</div><?
	}
}elseif($_GET['complite']){
	?><div class="success">Спасибо, сообщение отправлено</div><?
}
?><form action="#form" method="post" id="form">
	<input type="hidden" name="manager" value="<?=(int)$_REQUEST['manager']?>"><?
	for($i=0; $i<count($arField); $i++)
	{
		echo $LIB['FORM']->Element($arField[$i]['ID'], '<p class="field">%NAME%<br>%FIELD%</p>');
	}
	?><p class="field">Введите символы на картинки<span class="star">*</span><br><input type="text" name="K2CAPTCHA" value=""></p>
	<p><img src="/k2/captcha/?<?=session_id()?>"></p>
	<p class="form"><span class="star">*</span> &mdash; поля, обязательные для заполнения</p>
	<p class="form"><input type="submit" value="Отправить"></p>
</form><?
unset($_SESSION['K2CAPTCHA']);
?>