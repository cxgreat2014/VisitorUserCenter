<?php
if (!defined('UC_path')) {
    define('UC_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "\\1", str_replace('\\', '/', __FILE__)) . "/");
    define('site_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "", str_replace('\\', '/', __FILE__)) . "/");
}
require_once UC_path . 'System/dbconfig.php';
require_once UC_path . 'ControlPanel/class/vuc.php';
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

function vuc_genstr($length = 16)
{
// 密码字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {

        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}


function InstallPlugin_VisitorUserCenter()
{
    global $zbp;
    //数据库检测与创建
    $s = '';
    if (!$zbp->db->ExistTable($GLOBALS['table']['vuc_user'])) {
        $s .= $zbp->db->sql->CreateTable($GLOBALS['table']['vuc_user'], $GLOBALS['datainfo']['vuc_user']) . ';';
    }
    if (!$zbp->db->ExistTable($GLOBALS['table']['vuc_group'])) {
        $s .= $zbp->db->sql->CreateTable($GLOBALS['table']['vuc_group'], $GLOBALS['datainfo']['vuc_group']) . ';';

    }
    if (!$zbp->db->ExistTable($GLOBALS['table']['vuc_history'])) {
        $s .= $zbp->db->sql->CreateTable($GLOBALS['table']['vuc_history'], $GLOBALS['datainfo']['vuc_history']) . ';';
    }
    if (!$zbp->db->ExistTable($GLOBALS['table']['vuc_config'])) {
        $s .= $zbp->db->sql->CreateTable($GLOBALS['table']['vuc_config'], $GLOBALS['datainfo']['vuc_config']) . ';';
    }
    $zbp->db->QueryMulit($s);
    //初始化
    $vuc = new VUC();
    $vuc->SetConfig('normenu', '0');
    $vuc->SetConfig('noselect', '0');
    $vuc->SetConfig('nof5', '0');
    $vuc->SetConfig('nof12', '0');
    $vuc->SetConfig('noiframe', '1');
    $vuc->SetConfig('closesite', '0');
    $vuc->SetConfig('closetips', '网站正在维护，请稍后再访问');
    $vuc->SetConfig('sitehost', $_SERVER['HTTP_HOST']);
    $vuc->SetConfig('siteprocted', '0');
    //创建加密证书实行加密通讯
    $dn = array(
        "countryName" => "CN",
        "stateOrProvinceName" => "Beijing",
        "localityName" => "Beijing",
        "organizationName" => "MySelf",
        "organizationalUnitName" => "Whatever",
        "commonName" => "mySelf",
        "emailAddress" => "user@domain.com"
    );

    $privkeypass = 'cxg2014.bit'; //私钥密码
    $numberofdays = 365;     //有效时长
    $ckfn = vuc_genstr();
    $cerpath = $ckfn.".cer"; //生成证书路径
    $pfxpath = $ckfn.".pfx"; //密钥文件路径
    $vuc->SetConfig('RSA', $ckfn);


    //生成证书
    $privkey = openssl_pkey_new();
    $csr = openssl_csr_new($dn, $privkey);
    $sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays);
    openssl_x509_export($sscert, $csrkey); //导出证书$csrkey
    openssl_pkcs12_export($sscert, $privatekey, $privkey, $privkeypass); //导出密钥$privatekey
    //生成证书文件
    $fp = fopen($cerpath, "w");
    fwrite($fp, $csrkey);
    fclose($fp);
    //生成密钥文件
    $fp = fopen($pfxpath, "w");
    fwrite($fp, $privatekey);
    fclose($fp);
}


function UninstallPlugin_VisitorUserCenter()
{
}