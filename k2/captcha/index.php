<?php
error_reporting (0);
include('captcha.php');
if(isset($_SERVER['REQUEST_URI'])){
	session_start();
}
$captcha = new KCAPTCHA();
if($_SERVER['REQUEST_URI']){
	$_SESSION['K2CAPTCHA'] = $captcha->getKeyString();
}
?>