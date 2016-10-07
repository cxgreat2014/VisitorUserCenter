<?php
if (!defined('UC_path')) {
    define('UC_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "\\1", str_replace('\\', '/', __FILE__)) . "/");
    define('site_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "", str_replace('\\', '/', __FILE__)) . "/");
}
require_once site_path . 'zb_system/function/c_system_base.php';
$zbp->Load();

class VUC {
    private $version = 27;

    function __construct() {
        global $zbp;
        if (!$zbp->db->ExistTable($GLOBALS['table']['vuc_user']) || !$zbp->db->ExistTable($GLOBALS['table']['vuc_group'])
            || !$zbp->db->ExistTable($GLOBALS['table']['vuc_history'])||!$zbp->db->ExistTable($GLOBALS['table']['vuc_config'])) {
            $this->init();
        }
    }

    private function init() {
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
        $vuc->SetConfig('normenu', false);
        $vuc->SetConfig('noselect', false);
        $vuc->SetConfig('nof5', false);
        $vuc->SetConfig('nof12', false);
        $vuc->SetConfig('noiframe', true);
        $vuc->SetConfig('closesite', false);
        $vuc->SetConfig('closetips', '网站正在维护，请稍后再访问');
        $vuc->SetConfig('sitehost', $_SERVER['HTTP_HOST']);
        $vuc->SetConfig('siteprocted', false);

        $vuc->SetConfig('version', $this->version);
    }

    //user
    function CreatUser($DataArr) {
        global $zbp;
        $sql = $zbp->db->sql->Insert($GLOBALS['table']['vuc_user'], $DataArr);
        $zbp->db->Insert($sql);
    }

    function CheckUserName($UserName) {
        /*
         * true=OK,false=denied
         */
        return empty($this->GetUserByName($UserName));
    }

    function CheckInvcode($Invcode) {
        /*
         * true=OK,false=denied
         */
        return empty($this->GetUserByInvcode($Invcode));
    }

    function ChangeUserStatus($uid, $Status, $Notice) {
        if (!isset($uid) || $uid === "") {
            throw new Exception("No Uid Set!", 1);
        } elseif (empty($Status)) {
            throw new Exception("No Status Set!", 1);
        }
        global $jrp;
        $enc_dec=new Enc_Dec();
        $this->UpdateUser($uid, array('name'=>$enc_dec->GenStr(1).$this->GetUserByUid($uid)->name.$enc_dec->GenStr(1),'status' => $Status));
        $jrp->ChangeStatus(true);
        $jrp->SetHint("good", $Notice);
        $jrp->SendJsonWithDie();
    }

    function GetUserList($str = null, $value = null) {
        global $zbp;
        $where = null;
        if (!empty($str)) $where = array(array('=', $str, $value));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['vuc_user'], '*', $where);
        $array = $zbp->GetListCustom($GLOBALS['table']['vuc_user'], $GLOBALS['datainfo']['vuc_user'], $sql);
        return $array;
    }

    function GetUserByName($UserName) {
        return $this->GetUserList("name", $UserName);
    }

    function GetUserByInvcode($Invcode) {
        return $this->GetUserList('invcode', $Invcode);
    }

    function GetUserByUid($uid) {
        $result=$this->GetUserList('uid', $uid);
        if(empty($result))return null;
        return $result[0];
    }

    function GetUserLastLogin($uid) {
        $where = array(array('=', 'uid', $uid));
        $array = $this->GetHistoryList($where);
        return $array;
    }

    function UpdateUser($uid, $DataArr) {
        global $zbp;
        $where = array(array('=', 'uid', $uid));
        $sql = $zbp->db->sql->Update($GLOBALS['table']['vuc_user'], $DataArr, $where);
        $zbp->db->Update($sql);
    }

    //Group
    function CreatGroup($DataArr) {
        global $zbp;
        $sql = $zbp->db->sql->Insert($GLOBALS['table']['vuc_group'], $DataArr);
        $zbp->db->Insert($sql);
    }

    function GetGroupList($str = null, $value = null) {
        global $zbp;
        $where = null;
        if (!empty($str)) $where = array(array('=', $str, $value));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['vuc_group'], '*', $where);
        $array = $zbp->GetListCustom($GLOBALS['table']['vuc_group'], $GLOBALS['datainfo']['vuc_group'], $sql);
        return $array;
    }

    function GetGroupByName($gname) {
        return $this->GetGroupList('gname', $gname);
    }

    function UpdateGroupInfo($gid, $DataArr) {
        global $zbp;
        $where = array(array('=', 'gid', $gid));
        $sql = $zbp->db->sql->Update($GLOBALS['table']['vuc_group'], $DataArr, $where);
        $zbp->db->Update($sql);
    }

    //history
    function GetHistoryList($where = null, $order = null) {
        global $zbp;
        if (empty($order)) $order = array('time' => 'ASC');//'sean_IsUsed' => 'DESC',
        $sql = $zbp->db->sql->Select($GLOBALS['table']['vuc_history'], '*', $where, $order, null, null);
        $array = $zbp->GetListCustom($GLOBALS['table']['vuc_history'], $GLOBALS['datainfo']['vuc_history'], $sql);
        return $array;
    }

    function OutputLog($uid, $logmod, $logmsg) {
        global $zbp;
        $DataArr = array('uid' => $uid, 'logmod' => $logmod, 'logmsg' => $logmsg);
        $sql = $zbp->db->sql->Insert($GLOBALS['table']['vuc_history'], $DataArr);
        $zbp->db->Insert($sql);
    }

    function DeleteLog($logid) {
        global $zbp;
        $where = array(array('=', 'logid', $logid));
        $DataArr = array('type' => '已删除');
        $sql = $zbp->db->sql->Update($GLOBALS['table']['vuc_history'], $DataArr, $where);
        $zbp->db->Update($sql);
    }

    //config
    function SetConfig($key, $value) {
        global $zbp;
        $where = array(array('=', 'key', $key));
        $DataArr = array('key' => $key, 'value' => json_encode(array('value' => $value)));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['vuc_config'], 'id', $where, null, null, null);
        $array = $zbp->GetListCustom($GLOBALS['table']['vuc_config'], $GLOBALS['datainfo']['vuc_config'], $sql);
        if (empty($array)) {
            $zbp->db->Insert($zbp->db->sql->Insert($GLOBALS['table']['vuc_config'], $DataArr));
        } else {
            $zbp->db->Update($zbp->db->sql->Update($GLOBALS['table']['vuc_config'], $DataArr, $where));
        }
    }

    function GetConfig($key) {
        global $zbp;
        $where = array(array('=', 'key', $key));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['vuc_config'], 'value', $where);
        $array = $zbp->GetListCustom($GLOBALS['table']['vuc_config'], $GLOBALS['datainfo']['vuc_config'], $sql);
        $array=json_decode($array[0]->value);
        //if (empty($array)) return '';
        return $array->value;
    }
}

class Enc_Dec {
    private $pkey;
    private $cert;

    public function __construct() {
        $vuc = new VUC();
        if (!is_file($vuc->GetConfig('pfxpath'))) $this->GenCert();
        $priv_key = '';
        $priv_key = file_get_contents($vuc->GetConfig('pfxpath')); //获取密钥文件内容
        openssl_pkcs12_read($priv_key, $certs, $vuc->GetConfig('privkeypass')); //读取公钥、私钥
        $this->pkey = $certs['pkey']; //私钥
        $this->cert = $certs['cert'];

    }

    //Enc & Dec
    public function PrivateDecrypt($data) {
        $decrypted = '';
        openssl_private_decrypt(base64_decode($data), $decrypted, $this->pkey);
        return $decrypted;
    }

    public function PrivateEncrypt($data) {
        $encrypted = '';
        openssl_private_encrypt($data, $encrypted, $this->pkey);
        return base64_encode($encrypted);
    }

    public function PublicDecrypt($data) {
        $decrypted = '';
        openssl_public_decrypt(base64_decode($data), $decrypted, $this->cert);
        return $decrypted;
    }

    public function PublicEncrypt($data) {
        $encrypted = '';
        openssl_public_encrypt($data, $encrypted, $this->cert);
        return base64_encode($encrypted);
    }

    public function GenStr($length = 16) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    private function GenCert() {
        $vuc = new VUC();
        if (!empty($vuc->GetConfig('pfxpath')) && is_file($vuc->GetConfig('pfxpath'))) return;
        //创建数字证书实行加密通讯
        $dn = array(
            "countryName" => "CN",
            "stateOrProvinceName" => "Beijing",
            "localityName" => "Beijing",
            "organizationName" => "MySelf",
            "organizationalUnitName" => "Whatever",
            "commonName" => "mySelf",
            "emailAddress" => "user@domain.com"
        );

        $privkeypass = $this->GenStr(8); //私钥密码
        $numberofdays = 36502;     //有效时长
        $ckfn = $this->GenStr();
        $pfxpath = UC_path . 'System/' . $ckfn . ".pfx"; //密钥文件路径
        $vuc->SetConfig('pfxpath', $pfxpath);
        $vuc->SetConfig('privkeypass', $privkeypass);


        //生成证书
        $privkey = openssl_pkey_new();
        $csr = openssl_csr_new($dn, $privkey);
        $sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays);
        openssl_pkcs12_export($sscert, $privatekey, $privkey, $privkeypass); //导出密钥$privatekey
        //生成密钥文件
        $fp = fopen($pfxpath, "w");
        fwrite($fp, $privatekey);
        fclose($fp);
    }
}