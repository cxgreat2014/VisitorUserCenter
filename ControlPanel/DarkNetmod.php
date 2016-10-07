<?php
define('UC_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "\\1", str_replace('\\', '/', __FILE__)));
define('site_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "", str_replace('\\', '/', __FILE__)) . "/");
require_once UC_path . 'ControlPanel/main.php';
$blogtitle = 'VisitorUserCenter - 暗网模式';
global $zbp;
global $vuc;
require dirname(__FILE__) . '/header.php';
?>