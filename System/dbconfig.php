<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/2
 * Time: 11:32
 */
$table['vuc_user'] = '%pre%vuc_user';
$datainfo['vuc_user'] = array(
    'uid' => array('uid', 'integer', '', 0),
    'name' => array('name', 'string', 32, ''),
    'pwd' => array('pwd', 'string', '', ''),
    'type' => array('type', 'string', '', ''),
    'gid' => array('gid', 'integer', "", 0),
    'status' => array('status', 'string', 32, '禁止访问'),
    'email' => array('email', 'string', 128, ''),
    'invcode' => array('invcode', 'string', 6, ''),
    'token' => array('token', 'string', 32, '')
);

$table['vuc_group'] = '%pre%vuc_group';
$datainfo['vuc_group'] = array(
    'gid' => array('gid', 'integer', '', 0),
    'gname' => array('gname', 'string', 32, ''),
    'template' => array('template', 'string', 32, '禁止访问'),
    'spy' => array('spy', 'boolean', "", true),
    'oauth' => array('oauth', 'string', '', ''),//json oauthcate
    'status' => array('status', "string", 32, '禁止访问')
);

$table['vuc_history'] = '%pre%vuc_history';
$datainfo['vuc_history'] = array(
    'logid' => array('logid', 'integer', '', 0),
    'uid' => array('uid', 'integer', '', 0), //用户ID 0=system
    'time' => array('time', 'timestamp', '', 0), //
    'logmod' => array('logmod', 'string', '', ''), //日志类型，暂时有 local ipv4 ipv6 ipcn
    'logmsg' => array('logmsg', 'string', '', ''), //日志消息
    'type' => array('type', 'string', '', '正常')
);
$table['vuc_config'] = '%pre%vuc_config';
$datainfo['vuc_config'] = array(
    'id' => array('id', 'integer', '', 0),
    'key' => array('key', 'string', '', ''),
    'value' => array('value', 'string', '', '')
);