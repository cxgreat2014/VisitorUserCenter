<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/14
 * Time: 19:10
 */
class JsonReply {
    var $json = array();

    function ChangeStatus($status) {
        $this->json['status'] = $status;
    }

    function SetVtip($place, $msg) {
        $this->json['action'] = 'vtip';
        $this->json['vtip'] = array('place' => $place, 'msg' => $msg);
    }

    function SetAlert($msg,$Debug) {
        $this->json['action'] = 'alert';
        $this->json['msg'] = array('debug' => $Debug, 'msg' => $msg);
    }

    function SetHint($status,$msg){
        $this->json['action']='hint';
        $this->json['hint']=array('status'=>$status,'msg'=>$msg);
    }
    function SendJson(){
        if(empty($this->json['status']))$this->json['status']=true;
        echo json_encode($this->json);
    }
    function SendJsonWithDie(){
        $this->SendJson();
        die();
    }
}