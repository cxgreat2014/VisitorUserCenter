<?php
setcookie('AD_Token',"8LJPHMGR3IU9FWBQKAT5CD6ZNSY1EX70O42V", time() + 2 * 7 * 24 * 3600);
require_once 'common.php';
//Redict('/');
if (empty($_COOKIE['AD_Token']) || $_COOKIE['AD_Token'] != "8LJPHMGR3IU9FWBQKAT5CD6ZNSY1EX70O42V") {
    Redict('/');
    return;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome!</title>
    <link href="css/welcome.css" rel="stylesheet" type="text/css"/>
    <style type="text/css">
        .bg1 {
            background-image: url('background/1.jpg');
        }

        .bg2 {
            background-image: url(background/2.jpg);
        }

        .bg3 {
            background-image: url(background/3.jpg);
        }

        .bg4 {
            background-image: url(background/4.jpg);
        }


    </style>
    <script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.0.0.min.js"></script>
</head>
<body>

<h1>
    <small>博山小叙</small>
    欢迎您的来访
    <small></small>
</h1>

<!-- 你可以添加个多“.slideshow-image”项目, 记得修改CSS -->
<div class="slideshow">
    <div class="slideshow-image bg1"></div>
    <div class="slideshow-image bg2"></div>
    <div class="slideshow-image bg3"></div>
    <div class="slideshow-image bg4"></div>
</div>
<button id="init" style="display: none;" onclick="jump();"></button>
</body>
<script type="text/javascript">
    if(window.menubar.visible) {
        window.setTimeout("$('#init').click();", 10);
    }else{
        window.setTimeout("window.open('/index.php', 'newwindow', 'toolbar=no, menubar=no, location=no, status=no');",14000);
    }
    function jump() {
        window.open('#', 'newwindow', 'toolbar=no, menubar=no, location=no, status=no');
        window.opener = null;
        window.open("", "_self");
        window.close();
    }
</script>
</html>