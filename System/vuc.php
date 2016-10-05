<?php
if (!defined('UC_path')) {
    define('UC_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "\\1", str_replace('\\', '/', __FILE__)) . "/");
    define('site_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "", str_replace('\\', '/', __FILE__)) . "/");
}
require_once site_path . 'zb_system/function/c_system_base.php';
$zbp->Load();

class VUC {
    private $pkey;
    private $cert;

    public function __construct() {
        if (!empty($this->GetConfig('install'))) {
            $priv_key = file_get_contents($this->GetConfig('pfxpath')); //获取密钥文件内容
            openssl_pkcs12_read($priv_key, $certs, $this->GetConfig('privkeypass')); //读取公钥、私钥
            $this->pkey = $certs['pkey']; //私钥
            $this->cert = $certs['cert'];
        }
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
        $this->UpdateUser($uid, array('status' => $Status));
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
        return $this->GetUserList('uid', $uid);
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
    function SetConfig($key, $value, $ext = null) {
        global $zbp;
        $where = array(array('=', 'key', $key));
        $DataArr = array('key' => $key, 'value' => $value);
        if (!empty($ext)) $DataArr['ext'] = $ext;
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
        return $array[0]->value;
    }

    //Enc & Dec
    function PrivateDecrypt($data) {
        $decrypted = '';
        openssl_private_decrypt(base64_decode($data), $decrypted, $this->pkey);
        return $decrypted;
    }

    function PrivateEncrypt($data) {
        $encrypted = '';
        openssl_private_encrypt($data, $encrypted, $this->pkey);
        return base64_encode($encrypted);
    }

    function PublicDecrypt($data) {
        $decrypted = '';
        openssl_public_decrypt(base64_decode($data), $decrypted, $this->cert);
        return $decrypted;
    }

    function PublicEncrypt($data) {
        $encrypted = '';
        openssl_public_encrypt($data, $encrypted, $this->cert);
        return base64_encode($encrypted);
    }

    function GenStr($length = 16) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }
}
