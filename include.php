<?php
define("oauth2_path", dirname(__FILE__) . "/");
require oauth2_path . 'user/darknet_mod.php';
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
    'type' => array('type', 'string', '', '正常')
);
$table['plugin_oauth2_config'] = '%pre%plugin_oauth2_config';
$datainfo['plugin_oauth2_config'] = array(
    'id' => array('id', 'integer', '', 0),
    'key' => array('key', 'string', '', ''),
    'value' => array('value', 'string', '', ''),
    'ext' => array('ext', 'string', '', '')
);
//数据库定义结束
#注册插件
RegisterPlugin("oauth2", "ActivePlugin_oauth2");

function ActivePlugin_oauth2() {
    Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags', 'oauth2_MakeTemplatetags');
    Add_Filter_Plugin('Filter_Plugin_Index_Begin', 'oauth2_ECHO');
}

function oauth2_MakeTemplatetags() {
    global $zbp;
    $zbp->header .= '<script type="text/javascript">' . file_get_contents(dirname(__FILE__) . "/User/js/SiteCtrl.js") . '</script>' . "\r\n";
}

function InstallPlugin_oauth2() {
    global $zbp;
    oauth2_CreatTable();
    oauth2_init();
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

function oauth2_CreatTable() {
    global $zbp;
    //数据库检测与创建
    $s = '';
    if (!$zbp->db->ExistTable($GLOBALS['table']['plugin_oauth2_user'])) {
        $s .= $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user']) . ';';
    }
    if (!$zbp->db->ExistTable($GLOBALS['table']['plugin_oauth2_group'])) {
        $s .= $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group']) . ';';

    }
    if (!$zbp->db->ExistTable($GLOBALS['table']['plugin_oauth2_history'])) {
        $s .= $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_oauth2_history'], $GLOBALS['datainfo']['plugin_oauth2_history']) . ';';
    }
    if (!$zbp->db->ExistTable($GLOBALS['table']['plugin_oauth2_config'])) {
        $s .= $zbp->db->sql->CreateTable($GLOBALS['table']['plugin_oauth2_config'], $GLOBALS['datainfo']['plugin_oauth2_config']) . ';';
    }
    $zbp->db->QueryMulit($s);
}

function oauth2_init(){

}
function UninstallPlugin_oauth2() {
}