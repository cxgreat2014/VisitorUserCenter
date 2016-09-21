<?php
require_once 'common.php';

function oauth2_ECHO() {
    // $oauth2_path=dirname(dirname(__FILE__))."/";
    $cookiename = "AD_Token";
    $Stage1 = "XI14YUT9D8K5267N0WZCQFOL3AGJHSERVBMP";
    $Stage2 = "1HBCJZ8PSQ9F436YNXWUEIMDA0GK52VORTL7";
    $Stage3 = "U6HORG374KFE0BY5JQZLAMTWXNVPC1I9DS82";
    $Stage4 = "GJ1QPL0XIUOM74KATCDB6WR3Y58SEF29HNZV";
    $Stage5 = "8LJPHMGR3IU9FWBQKAT5CD6ZNSY1EX70O42V";
    $Stage6 = "D6IGT1Y97KUQXBAJ52F4LSH3OPZRCVN08EMW";
    /*
        $welcomecss=file_get_contents($oauth2_path . "css/welcome.css");
        $bg1=GetEncodeImgCss($oauth2_path.'user/background/1.jpg');
        $bg2=GetEncodeImgCss($oauth2_path.'user/background/2.jpg');
        $bg3=GetEncodeImgCss($oauth2_path.'user/background/3.jpg');
        $bg4=GetEncodeImgCss($oauth2_path.'user/background/4.jpg');
    */
    $exp_time = time() + 80;//time() + 2 * 7 * 24 * 3600;
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
            break;
        case $Stage5:
            return;
        default:
            setcookie($cookiename, $Stage4, $exp_time);
    }
    oauth2_ECHO();
    die();
}
?>

