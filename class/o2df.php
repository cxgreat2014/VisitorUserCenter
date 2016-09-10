<?php
$table['plugin_oauth2_user'] = '%pre%plugin_oauth2_user';
$datainfo['plugin_oauth2_user'] = array(
    'uid' => array('uid', 'integer', '', 0),
    'name' => array('name', 'string', 32, ''),
    'pwd' => array('pwd', 'string', 32, ''),
    'type' => array('type', 'string', '', ''),
    'gid' => array('gid', 'integer', "", 0),
    'status' => array('status', 'string', 32, '禁止访问'),
    'email' => array('email', 'string', 128, ''),
    'invcode' => array('invcode', 'string', 6, ''),
    'token' => array('token', 'string', 32, '')
);

$table['plugin_oauth2_group'] = '%pre%plugin_oauth2_group';
$datainfo['plugin_oauth2_group'] = array(
    'gid' => array('gid', 'integer', '', 0),
    'gname' => array('gname', 'string', 32, ''),
    'template' => array('template', 'string', 32, '禁止访问'),
    'spy' => array('spy', 'boolean', "", true),
    'oauth' => array('oauth', 'string', '', ''),//json oauthcate
    'status' => array('status', "string", 32, '禁止访问')
);

$table['plugin_oauth2_history'] = '%pre%plugin_oauth2_history';
$datainfo['plugin_oauth2_history'] = array(
    'logid' => array('logid', 'integer', '', 0),
    'uid' => array('uid', 'integer', '', 0), //用户ID 0=system
    'time' => array('time', 'timestamp', '', 0), //时间戳
    'logtype' => array('logtype', 'string', '', ''), //日志类型，暂时有 local ipv4 ipv6 ipcn
    'logmsg' => array('logmsg', 'string', '', '') //日志消息
);