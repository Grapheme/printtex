<!DOCTYPE HTML>
<html>
	<head>
        <meta charset="UTF-8">
        <title><!-- $TITLE$ --></title>
        <meta name="keywords" content="<!-- $KEYWORD$ -->">
		<meta name="description" content="<!-- $DESCRIPTION$ -->">
		<link href="/css/lightbox.css" rel="stylesheet" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="/jc/style.css">
        <link rel="stylesheet" href="/jc/dialog.css">
		<link rel="stylesheet" href="/jc/jquery-ui.css">
		<link rel="stylesheet" href="/jc/slider-guild.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
	<script src="/js/lightbox.js"></script>
        <script src="/jc/prefixfree.min.js"></script>
        <script src="/jc/jquery.formstyler.min.js"></script>
        <script src="/jc/java.js"></script>
        <script src="/jc/slides.js"></script>
<!-- BEGIN JIVOSITE CODE {literal} -->
<script type="text/javascript">
(function() { var widget_id = '26221';
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss); })(); </script> 
<!-- {/literal} END JIVOSITE CODE -->
	</head>
	<body>
        <div id="main-wrapper">
            <div id="header-wrapper">
                <div id="logo">
                    <a href="/"><img src="/i/logo.png" width="231" height="102" alt="logo" /></a>
                </div>
                <div id="slogan">
                    <img src="/i/slogan.png" width="392" height="80" alt="slogan" />
                </div>
                <div id="splash">
                    <img src="/i/splash-<?randSplash()?>.png" alt="splash" />
                </div>
                <div id="contacts">
                    <p><?=$CURRENT['SITE']['ADDRESS']?><br>
                    <?=$CURRENT['SITE']['PHONE']?></p>
                </div>
            </div>

            <?$LIB['NAV']->Menu(8, array('ACTIVE' => 1, 'PARENT' => 0))?>
            <div id="content-wrapper" class="content">
				<h1><?
				if($CURRENT['SECTION']['H1']){
					echo $CURRENT['SECTION']['H1'];
				}else{
					$LIB['NAV']->Back(2);
				}
				?></h1>