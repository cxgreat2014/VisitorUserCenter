<?php

class Oauth2 {
    function CreatUser($DataArr) {
        global $zbp;
        $sql = $zbp->db->sql->Insert($GLOBALS['table']['plugin_oauth2_user'], $DataArr);
        $zbp->db->Insert($sql);
    }

    function CheckUserName($UserName) {
        /*
         * true=OK,false=denied
         */
        global $oauth2;
        $array = $this->GetUserByName($UserName);
        return empty($array);
    }

    function CheckInvcode($Invcode) {
        /*
         * true=OK,false=denied
         */
        global $zbp;
        $where = array(array('=', 'invcode', $Invcode));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'], '*', $where);
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql);
        return empty($array);
    }

    function ChangeUserStatus($uid, $Status, $Notice) {
        if (!isset($uid) || $uid === "") {
            throw new Exception("No Uid Set!", 1);
        } elseif (empty($Status)) {
            throw new Exception("No Status Set!", 1);
        }
        global $zbp,$jrp;
        $array = array('status' => $Status);
        $where = array(array('=', 'uid', $uid));
        $sql = $zbp->db->sql->Update($GLOBALS['table']['plugin_oauth2_user'], $array, $where);
        $zbp->db->Update($sql);
        $jrp->ChangeStatus(true);
        $jrp->SetHint($Status,$Notice);
        $jrp->SendJsonWithDie();
    }

    function GetUserList() {
        global $zbp;
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'], '*');
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql);
        return $array;
    }

    function GetUserByName($UserName) {
        global $zbp;
        $where = array(array('=', 'name', $UserName));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'], '*', $where);
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql);
        return $array;
    }

    function GetUserLastLogin($uid) {
        global $zbp;
        $where = array(array('=', 'uid', $uid));
        $order = array('time' => 'DESC');
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_history'], '*', $where, $order, null, null);
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_history'], $GLOBALS['datainfo']['plugin_oauth2_history'], $sql);
        return $array;
    }

    function CreatGroup($DataArr) {
        global $zbp;
        $sql = $zbp->db->sql->Insert($GLOBALS['table']['plugin_oauth2_group'], $DataArr);
        $zbp->db->Insert($sql);
    }

    function GetGroupList() {
        global $zbp;
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], '*');
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
        return $array;
    }

    function GetGroupByName($gname) {
        global $zbp;
        $where = array(array('=', 'gname', $gname));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], '*', $where);
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
        return $array;
    }

    function UpdateGroupInfo($gid, $DataArr) {
        global $zbp;
        $where = array(array('=', 'gid', $gid));
        $sql = $zbp->db->sql->Update($GLOBALS['table']['plugin_oauth2_group'], $DataArr, $where);
        $zbp->db->Update($sql);
    }
}
