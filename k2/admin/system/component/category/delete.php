<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/header.php');

if($_ID){
	$LIB['COMPONENT_CATEGORY']->Delete($_ID, $_COMPONENT);
}
if($_POST['ID']){
	for($i=0; $i<count($_POST['ID']); $i++)
	{
		$LIB['COMPONENT_CATEGORY']->Delete($_POST['ID'][$i], $_COMPONENT);
	}
}

Redirect('/k2/admin/system/component/?field='.$_FIELD.'&component='.$_COMPONENT.'&collection='.$_COLLECTION.'&category='.$_CATEGORY);

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/component/footer.php');
?>