<?php
if (!defined('UC_path')) {
    define('UC_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "\\1", str_replace('\\', '/', __FILE__)));
    define('site_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "", str_replace('\\', '/', __FILE__)) . "/");
}
require_once UC_path . 'System/dbconfig.php';
require_once UC_path . 'System/vuc.php';
require_once UC_path . 'System/loadmod.php';
#注册插件
RegisterPlugin("vuc", "ActivePlugin_VisitorUserCenter");

function ActivePlugin_VisitorUserCenter()
{
    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'vuc_Js_Add');
    Add_Filter_Plugin('Filter_Plugin_Index_Begin', 'vuc_Index_Begin');
    Add_Filter_Plugin('Filter_Plugin_Logs', 'vuc_Logs');
}

function vuc_Js_Add()
{
    global $zbp;
    $action = 'root';
    if ($zbp->CheckRights($action)) {
        echo file_get_contents(UC_path . "User/js/SiteCtrl.js") . "\r\n";
    }
}

function vuc_Index_Begin()
{

}

function vuc_Logs($log, $e)
{
    $vuc = new VUC();
    $vuc->OutputLog('0', 'system_log' . ($e == true ? '' : '_err'), $log);
}

function ReNewSiteCtrlJSFile()
{
    $vuc = new VUC();
    $SiteCtrljs = fopen("../../User/js/SiteCtrl.js", "w") or die("Unable to open file!");

    if ($vuc->GetConfig('normenu'))
        fwrite($SiteCtrljs, "
document.oncontextmenu=function(e){
    return false;
};");
    if ($vuc->GetConfig('noselect'))
        fwrite($SiteCtrljs, '
document.onselectstart=function(){
	return false;
};
$("body").attr({style:"-webkit-touch-callout:none;-webkit-user-select:none;-moz-user-select:none;user-select:none;"});');

//-------------按键拦截------------
    if ($vuc->GetConfig('nof5') || $vuc->GetConfig('nof12') || $vuc->GetConfig('nocs')) {
        $js = "
document.onkeydown = function(e){
    if (";
        if ($vuc->GetConfig('nof5'))
            $js .= "e.keyCode == 116 ||(e.ctrlKey && e.keyCode==82)";
        if ($vuc->GetConfig('nof12')) {
            if ($vuc->GetConfig('nof5'))
                $js .= "||";
            $js .= "e.keyCode == 123 || (e.shiftKey && e.ctrlKey && (e.keyCode==73))";
        }
        if ($vuc->GetConfig('nocs')) {
            if ($vuc->GetConfig('nof5') || $vuc->GetConfig('nof12'))
                $js .= "||";
            $js .= "e.ctrlKey && (e.keyCode==83)";
        }
        $js .= "){
		e.preventDefault();  
	}
}";
        fwrite($SiteCtrljs, $js);
    }

    if ($vuc->GetConfig('noiframe'))
        fwrite($SiteCtrljs, "
if (window.location !== window.top.location) {
	window.top.location = window.location;
}
");
    if ($vuc->GetConfig('closesite'))
        fwrite($SiteCtrljs, '
$("body").html("<div style=\"position:fixed;top:0;left:0;width:100%;height:100%;text-align:center;background:#fff;padding-top:150px;z-index:99999;\">' . $vuc->GetConfig('closetips') . '</div>");');

    fclose($SiteCtrljs);
}




function InstallPlugin_VisitorUserCenter()
{
    global $zbp;

}


function UninstallPlugin_VisitorUserCenter()
{
}