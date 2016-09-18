<?php
//数据库定义开始
$table['plugin_oauth2_user'] = '%pre%plugin_oauth2_user';
$datainfo['plugin_oauth2_user'] = array(
    'uid' => array('uid', 'integer', '', 0),
    'name' => array('name', 'string', 32, ''),
    'pwd' => array('pwd', 'string', 32, ''),
    'type' => array('type', 'string', '', ''),
    'gid' => array('gid', 'integer', "", 0),
    'status' => array('status', 'string', 32, '禁止访问'),
    'email' => array('email', 'string', 128, ''),
    'invcode' => array('invcode', 'string', 6, ''),
    'token' => array('token', 'string', 32, '')
);

$table['plugin_oauth2_group'] = '%pre%plugin_oauth2_group';
$datainfo['plugin_oauth2_group'] = array(
    'gid' => array('gid', 'integer', '', 0),
    'gname' => array('gname', 'string', 32, ''),
    'template' => array('template', 'string', 32, '禁止访问'),
    'spy' => array('spy', 'boolean', "", true),
    'oauth' => array('oauth', 'string', '', ''),//json oauthcate
    'status' => array('status', "string", 32, '禁止访问')
);

$table['plugin_oauth2_history'] = '%pre%plugin_oauth2_history';
$datainfo['plugin_oauth2_history'] = array(
    'logid' => array('logid', 'integer', '', 0),
    'uid' => array('uid', 'integer', '', 0), //用户ID 0=system
    'time' => array('time', 'timestamp', '', 0), //
    'logmod' => array('logmod', 'string', '', ''), //日志类型，暂时有 local ipv4 ipv6 ipcn
    'logmsg' => array('logmsg', 'string', '', ''), //日志消息
    'type'=>array('type','string','','正常')
);
//数据库定义结束
#注册插件
RegisterPlugin("oauth2", "ActivePlugin_oauth2");

function ActivePlugin_oauth2() {
    Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags', 'oauth2_MakeTemplatetags');
    Add_Filter_Plugin('Filter_Plugin_Index_Begin','oauth2_ECHO');
}

function oauth2_MakeTemplatetags() {
    global $zbp;
    $zbp->header .= '<script type="text/javascript">' . file_get_contents(dirname(__FILE__) . "/js/SiteCtrl.js") . '</script>' . "\r\n";
}

function InstallPlugin_oauth2() {
    global $zbp;
    oauth2_CreatTable();
    //配置初始化
    $zbp->Config('oauth2')->normenu = '0';
    $zbp->Config('oauth2')->noselect = '0';
    $zbp->Config('oauth2')->nof5 = '0';
    $zbp->Config('oauth2')->nof12 = '0';
    $zbp->Config('oauth2')->noiframe = '1';
    $zbp->Config('oauth2')->closesite = '0';
    $zbp->Config('oauth2')->closetips = '网站正在维护，请稍后再访问';

    $zbp->Config('oauth2')->siteprocted = false;
    $zbp->SaveConfig('oauth2');
}
function oauth2_CreatTable(){
    global $zbp;
    //数据库检测与创建
    if (!$zbp->db->ExistTable($GLOBALS['table']['plugin_oauth2_user'])) {
        $s = $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user']);
        $zbp->db->QueryMulit($s);
    }
    if (!$zbp->db->ExistTable($GLOBALS['table']['plugin_oauth2_group'])) {
        $s = $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group']);
        $zbp->db->QueryMulit($s);
    }
    if (!$zbp->db->ExistTable($GLOBALS['table']['plugin_oauth2_history'])) {
        $s = $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_oauth2_history'], $GLOBALS['datainfo']['plugin_oauth2_history']);
        $zbp->db->QueryMulit($s);
    }
}
function oauth2_ECHO(){
    function Redict($url) {
        header("location:" . $url);
    }

    function Iframe($url) {
        echo '<html><iframe src="' . $url . '" style="position:absolute; left:0; top:0; height:100%; width:100%; border:0;" scrolling="no"></iframe></html>';
    }
    $cookiename = "AD_Token";
    $Stage1 = "XI14YUT9D8K5267N0WZCQFOL3AGJHSERVBMP";
    $Stage2 = "1HBCJZ8PSQ9F436YNXWUEIMDA0GK52VORTL7";
    $Stage3 = "U6HORG374KFE0BY5JQZLAMTWXNVPC1I9DS82";
    $Stage4 = "GJ1QPL0XIUOM74KATCDB6WR3Y58SEF29HNZV";
    $exp_time = time() + 60;//time() + 2 * 7 * 24 * 3600;
    if (!empty($_COOKIE[$cookiename])) {
        $Token = $_COOKIE[$cookiename];
        switch ($Token) {
            case $Stage1:
                setcookie($cookiename, $Stage2, $exp_time);
                Redict("https://www.google.com/");
                break;
            case $Stage2:
                setcookie($cookiename, $Stage3, $exp_time);
                Redict("http://www.houseofdigital.com/");
                break;
            case $Stage3:
                setcookie($cookiename, $Stage4, $exp_time);
                Iframe('https://www.baidu.com/');
                break;
            case $Stage4:
                setcookie($cookiename, $Stage4, time() + 2 * 7 * 24 * 3600);
                echo '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Welcome!</title>
<style type="text/css">
*{margin:0;padding:0;}

.slideshow {
  position: absolute;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
}

.slideshow-image {
  position: absolute;
  width: 100%;
  height: 100%;
  background: no-repeat 50% 50%;
  background-size: cover;
  -webkit-animation-name: kenburns;
          animation-name: kenburns;
  -webkit-animation-timing-function: linear;
          animation-timing-function: linear;
  -webkit-animation-iteration-count: infinite;
          animation-iteration-count: infinite;
  -webkit-animation-duration: 16s;
          animation-duration: 16s;
  opacity: 1;
  -webkit-transform: scale(1.2);
          transform: scale(1.2);
}
.slideshow-image:nth-child(1) {
  -webkit-animation-name: kenburns-1;
          animation-name: kenburns-1;
  z-index: 3;
}
.slideshow-image:nth-child(2) {
  -webkit-animation-name: kenburns-2;
          animation-name: kenburns-2;
  z-index: 2;
}
.slideshow-image:nth-child(3) {
  -webkit-animation-name: kenburns-3;
          animation-name: kenburns-3;
  z-index: 1;
}
.slideshow-image:nth-child(4) {
  -webkit-animation-name: kenburns-4;
          animation-name: kenburns-4;
  z-index: 0;
}

@-webkit-keyframes kenburns-1 {
  0% {
    opacity: 1;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  1.5625% {
    opacity: 1;
  }
  23.4375% {
    opacity: 1;
  }
  26.5625% {
    opacity: 0;
    -webkit-transform: scale(1);
            transform: scale(1);
  }
  100% {
    opacity: 0;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  98.4375% {
    opacity: 0;
    -webkit-transform: scale(1.21176);
            transform: scale(1.21176);
  }
  100% {
    opacity: 1;
  }
}

@keyframes kenburns-1 {
  0% {
    opacity: 1;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  1.5625% {
    opacity: 1;
  }
  23.4375% {
    opacity: 1;
  }
  26.5625% {
    opacity: 0;
    -webkit-transform: scale(1);
            transform: scale(1);
  }
  100% {
    opacity: 0;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  98.4375% {
    opacity: 0;
    -webkit-transform: scale(1.21176);
            transform: scale(1.21176);
  }
  100% {
    opacity: 1;
  }
}
@-webkit-keyframes kenburns-2 {
  23.4375% {
    opacity: 1;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  26.5625% {
    opacity: 1;
  }
  48.4375% {
    opacity: 1;
  }
  51.5625% {
    opacity: 0;
    -webkit-transform: scale(1);
            transform: scale(1);
  }
  100% {
    opacity: 0;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
}
@keyframes kenburns-2 {
  23.4375% {
    opacity: 1;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  26.5625% {
    opacity: 1;
  }
  48.4375% {
    opacity: 1;
  }
  51.5625% {
    opacity: 0;
    -webkit-transform: scale(1);
            transform: scale(1);
  }
  100% {
    opacity: 0;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
}
@-webkit-keyframes kenburns-3 {
  48.4375% {
    opacity: 1;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  51.5625% {
    opacity: 1;
  }
  73.4375% {
    opacity: 1;
  }
  76.5625% {
    opacity: 0;
    -webkit-transform: scale(1);
            transform: scale(1);
  }
  100% {
    opacity: 0;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
}
@keyframes kenburns-3 {
  48.4375% {
    opacity: 1;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  51.5625% {
    opacity: 1;
  }
  73.4375% {
    opacity: 1;
  }
  76.5625% {
    opacity: 0;
    -webkit-transform: scale(1);
            transform: scale(1);
  }
  100% {
    opacity: 0;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
}
@-webkit-keyframes kenburns-4 {
  73.4375% {
    opacity: 1;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  76.5625% {
    opacity: 1;
  }
  98.4375% {
    opacity: 1;
  }
  100% {
    opacity: 0;
    -webkit-transform: scale(1);
            transform: scale(1);
  }
}
@keyframes kenburns-4 {
  73.4375% {
    opacity: 1;
    -webkit-transform: scale(1.2);
            transform: scale(1.2);
  }
  76.5625% {
    opacity: 1;
  }
  98.4375% {
    opacity: 1;
  }
  100% {
    opacity: 0;
    -webkit-transform: scale(1);
            transform: scale(1);
  }
}


h1 {
  position: absolute;
  top: 50%;
  left: 50%;
  -webkit-transform: translate3d(-50%, -50%, 0);
          transform: translate3d(-50%, -50%, 0);
  z-index: 99;
  text-align: center;
  font-family: Raleway, sans-serif;
  font-weight: 300;
  text-transform: uppercase;
  background-color: rgba(255, 255, 255, 0.75);
  box-shadow: 0 1em 2em -1em rgba(0, 0, 0, 0.5);
  padding: 1em 2em;
  line-height: 1.5;
}
h1 small {
  display: block;
  text-transform: lowercase;
  font-size: .7em;
}
h1 small:first-child {
  border-bottom: 1px solid rgba(0, 0, 0, 0.25);
  padding-bottom: .5em;
}
h1 small:last-child {
  border-top: 1px solid rgba(0, 0, 0, 0.25);
  padding-top: .5em;
}
</style>
</head>
<body>

<h1><small>博山小叙</small>欢迎您的来访<small></small></h1>

<!-- 你可以添加个多“.slideshow-image”项目, 记得修改CSS -->
<div class="slideshow">
	<div class="slideshow-image" style="background-image: url(\'/zb_users/plugin/oauth2/background/2.jpg\')"></div>
	<div class="slideshow-image" style="background-image: url(\'/zb_users/plugin/oauth2/background/4.jpg\')"></div>
	<div class="slideshow-image" style="background-image: url(\'/zb_users/plugin/oauth2/background/3.jpg\')"></div>
	<div class="slideshow-image" style="background-image: url(\'/zb_users/plugin/oauth2/background/1.jpg\')"></div>
</div>

</body>
<script>
window.setTimeout("window.location=\'/index.php\'",14000); 
</script>
</html>

';
        }
    } else {
        setcookie($cookiename, $Stage1, time() + 2 * 7 * 24 * 3600);
    }
    die();
}
function UninstallPlugin_oauth2() {
}