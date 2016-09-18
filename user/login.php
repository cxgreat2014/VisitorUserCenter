<?php /*
require '../../../zb_system/function/c_system_base.php';
$zbp->Load();*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>登录</title>
    <link rel="stylesheet" type="text/css" href="./css/style.min.css?v=2.10">
    <link rel="stylesheet" type="text/css" href="./css/aui.css"/>
    <script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.0.0.min.js"></script>
    <script src="//static.geetest.com/static/tools/gt.js"></script>
    <iframe id="iframe" sandbox="allow-same-origin" style="display: none"></iframe>
    <script type="text/javascript" src="./class/common.php"></script>
    <script type="text/javascript" id="ip138"
            src="//api.ip138.com/query/?ip=&oid=75&callback=ajquery&mid=65600&sign=ef6ac49df59eca9424f0c0777a82c616"></script>
</head>
<body class="gray-bg">
<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <div><h1 class="logo-name" style="font-size:65px">博山小叙</h1></div>
        <form class="m-t" name="f" method="post" action="./class/VerifyUserToken.php">
            <div class="form-group">
                <div class="aui-input-row">
                    <i class="aui-input-addon  aui-iconfont aui-icon-people"></i>
                    <input type="text" class="form-control aui-input" name="user" id="user" value="" placeholder="用户名"
                           required=""/>
                </div>
            </div>

            <div class="form-group">
                <div class="aui-input-row">
                    <i class="aui-input-addon  aui-iconfont aui-icon-safe"></i>
                    <input type="password" name="pw" id="pwd" class="form-control aui-input" placeholder="邀请码"
                           required="">
                </div>
            </div>
            <div id="embed-captcha"></div>
            <p id="wait" class="show">正在加载验证码......</p>
            <p id="notice" class="hide">请先拖动验证码到相应位置</p>
            <input id="embed-submit" type="submit" value=" 登 录" class="btn btn-primary block full-width m-b"
                   style="margin-top:10px;">
        </form>
    </div>
    <script type="text/javascript" src="./js/VaildCode.js"></script>
</body>
</html>
