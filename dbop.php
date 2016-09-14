<?php
class Oauth2{
    function GetUserList(){
        global $zbp;
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'], '*');
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql);
        return $array;
    }
    function   GetGroupList(){
        global $zbp;
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], '*');
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
        return $array;
    }
    function GetUserLastLogin($uid){
        global $zbp;
        $where = array(array('=', 'uid', $uid));
        $order = array('time' => 'DESC');
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_history'], '*', $where, $order, null, null);
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_history'], $GLOBALS['datainfo']['plugin_oauth2_history'], $sql);
        return $array;
    }
    function GetUserByName($UserName){
        global $zbp;
        $where = array(array('=', 'name', $UserName));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'], '*', $where);
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql);
        return $array;
    }
}
