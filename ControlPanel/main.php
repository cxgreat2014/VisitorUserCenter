<?php
require site_path . '/zb_system/function/c_system_base.php';
require site_path . '/zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('VisitorUserCenter')) {
    $zbp->ShowError(48);
    die();
}
require site_path . '/zb_system/admin/admin_header.php';
require site_path . '/zb_system/admin/admin_top.php';
$vuc = new VUC();