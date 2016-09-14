<?php
require dirname(__FILE__) . '/class/o2df.php';
#注册插件
RegisterPlugin("oauth2", "ActivePlugin_oauth2");

function ActivePlugin_oauth2() {
    Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags', 'oauth2_MakeTemplatetags');
}

function oauth2_MakeTemplatetags() {
    global $zbp;
    $zbp->header .= '<script type="text/javascript">' . file_get_contents(dirname(__FILE__) . "/js/SiteCtrl.js") . '</script>' . "\r\n";
}

function InstallPlugin_oauth2() {
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

function UninstallPlugin_oauth2() {
}