<?php
/*
echo hash('sha256', 'abc');
echo hash('sha512', 'abc');
// md5, sha1.. �ȵ�Ҳ�������ôˌ���
echo hash('md5', 'abc');
echo hash('sha1', 'abc');*/
$json = array();
$json['status'] = false;
$json['reason'][] = array('place' => 'name', 'msg' => 'aaa');
$json['reason'][] = array('place' => 'name', 'msg' => 'aaac');
$json['reason'][] = array('place' => 'invcode', 'msg' => 'aaa');
$json['reason'][] = array('place' => 'name', 'msg' => 'aaa');
$json = json_encode($json);
print $json;

?>