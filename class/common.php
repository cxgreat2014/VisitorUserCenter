<?php
require '../../../../zb_system/function/c_system_base.php';
$zbp->Load();

$reference = parse_url($_SERVER['HTTP_REFERER']);
if ($zbp->Config('oauth2')->siteprocted && $reference['host'] != 'dev.junziruogong.xyz') {
    header('HTTP/1.1 403 Access Denied');
    die();
} elseif ($zbp->Config('oauth2')->siteprocted && $_SERVER["HTTP_REFERER"] != "https://dev.junziruogong.xyz/zb_users/plugin/ZBlogOauth2/oauth2.php") {
    echo file_get_contents("../js/common.js");
    die();
} else {
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        echo "/*! jQuery v1.9.0 | (c) 2005, 2012 jQuery Foundation, Inc. | jquery.org/license */\n";
        echo file_get_contents("../js/ip.js");
    } elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
        //TODO: SAVE USER IP DATA
        //Some JS Problem Maybe In Here
        //phpinfo();dirname(dirname(__FILE__))
        //echo 'alert("'.$_POST["method"].":".$_POST["ip"].'");';
        //echo "return;";
    }
}
?>