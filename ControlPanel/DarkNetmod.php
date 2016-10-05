<?php
$blogpath = str_replace($_SERVER['PHP_SELF'], "", $_SERVER['SCRIPT_FILENAME']);
require $blogpath . '/zb_system/function/c_system_base.php';
require $blogpath . '/zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('oauth2')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'oauth2 - 暗网模式';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
require 'class/vuc.php';
$oauth = new Oauth2();
$oauth->SetConfig("version", '1.7', "1.1");
print_r($oauth->GetConfig('version'));