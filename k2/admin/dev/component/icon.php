<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('TEMPLATE');

?><table id="componentTable">
<tr><?
$arFile = scandir($_SERVER['DOCUMENT_ROOT'].'/k2/admin/i/component/');
for($i=0; $i<count($arFile); $i++)
{	if(preg_match("#(.+)\.png$#", $arFile[$i], $arMath)){		?><td><img src="/k2/admin/i/component/<?=$arFile[$i]?>" name="<?=$arMath[1]?>" width="16" height="16" alt=""></td><?
		$n++;
	}
	if(!($n%9)){		?></tr><tr><?
	}
}
?>
</tr>
</table>