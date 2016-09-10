<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('oauth2')) {
    $zbp->ShowError(48);
    die();
}

function CheckParamIsSet() {
    $total = 0;
    $args = func_get_args();
    for ($i = 0; $i < count($args); $i++) {
        if (is_string($args[$i])) {
            if (!isset($_POST[$args[$i]]) || $_POST[$args[$i]] == "") {
                header('HTTP/1.1 400 Post ' . $args[$i] . ' Error');
                header('status: 400 Post ' . $args[$i] . ' Error');
                echo '<b>Post ' . $args[$i] . ' Error</b>';
                die();
            } elseif (preg_match("/[\'.,:;*?~`!#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $_POST[$args[$i]])) {
                header('HTTP/1.1 400 Post ' . $args[$i] . ' Error. It Has Special Word');
                header('status: 400 Post ' . $args[$i] . ' Error. It Has Special Word');
                echo '<b>Post ' . $args[$i] . ' Error. It Has Special Word</b>';
                die();
            }
        }
    }
    return $total;
}

function SendAlertJS($message) {
    echo 'alert("' . $message . '");';
}

function GenerateHintJS($status, $msg) {
    return '$(\'<div class="hint"><p class="hint hint_' . $status . '">' . (empty($msg) ? "操作成功完成" : $msg) . '</p></div>\')' .
    '.insertBefore($("div#divMain"));$("div.hint:visible").delay(3500).hide(1500,function(){this.remove()});';
}

function SendGetRadomString() {
    echo 'getRandomString();';
}

function CheckUserName($UserName) {
    //true=OK,false=denied
    global $zbp;
    $where = array(array('=', 'name', $UserName));
    $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'], '*', $where);
    $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql);
    return empty($array);
}

function CheckInvcode($Invcode) {
    //true=OK,false=denied
    global $zbp;
    $where = array(array('=', 'invcode', $Invcode));
    $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'], '*', $where);
    $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql);
    return empty($array);
}


function ChangeUserStatus($uid, $Status, $Notice) {
    if (!isset($uid) || $uid === "") {
        throw new Exception("No Uid Set!", 1);
    } elseif (!isset($Status) || $Status === "") {
        throw new Exception("No Status Set!", 1);
    }
    global $zbp;
    $where = array(array('=', 'uid', $uid));
    $sql = $zbp->db->sql->Select('%pre%plugin_oauth2_user', 'utype', $where, null, null, null);
    $array = reset($zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql));
    $array = json_decode($array->utype);
    $array->status = $Status;
    $array = array('utype' => json_encode($array));
    $where = array(array('=', 'uid', $uid));
    $sql = $zbp->db->sql->Update($GLOBALS['table']['plugin_oauth2_user'], $array, $where);
    $zbp->db->Update($sql);
    $zbp->SetHint('good', $Notice);
}

/*
case"CreatNew":
    $str = "<style type='text/css'>p {padding: 0.5em 0 0.5em 0;line-height: 1.5em;}</style>";
    $str .= '<p style="margin-top: 9px;"><span class="title">用户名：&nbsp;</span><input type="text" id="name" />';
    $str .= '<p><span class="title">用户组：&nbsp;</span><select class="edit" style="width: 179px;" size="1" id="gs">';
    $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], '*');
    $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
    foreach ($array as $key => $reg) {
        $str .= '<option value="' . $reg->gid . '" ' . '>' . $reg->gname . '</option>';
    }
    $str .= '</select></p>';
    $str .= '<p><span class="title">邀请码：&nbsp;</span><input type="text" max="32" min="6" id="invcode"/>';
    $str .= '<a style="cursor:pointer;"><img style="vertical-align: middle;" src="./image/gencode.png"  alt="一键生成邀请码" title="一键生成邀请码" width="32" onclick="getRandomString(6)"></a></p>';

    $str .= '<p style="margin-left:104px;"><a style="cursor:pointer;"><img style="vertical-align: middle;" src="./image/ok.png"  alt="提交" title="提交" width="48" onclick="js_submit()"></a></p>';
    echo $str;
} elseif (!isset($_GET["CreatNew"])) {
    phpinfo();
}*/


CheckParamIsSet("action");
switch ($_POST['action']) {
    //用户操作类
    case "CreatUser":
        CheckParamIsSet("name", "type", "invcode");
        $name = $_POST['name'];
        $type = $_POST['type'];
        $invcode = $_POST['invcode'];
        if (strlen($name) > 32) {
            SendAlertJS('用户名最长32个字符，请重新输入');
            die();
        }
        if (!CheckUserName($name)) {
            SendAlertJS('用户：' . $name . ' 已存在，请重新输入或生成');
            die();
        }
        if (strlen($invcode) != 6) {
            SendAlertJS('邀请码长度必须为6，请重新输入或生成');
            die();
        }
        if (!CheckInvcode($invcode)) {
            SendAlertJS('邀请码：' . $invcode . ' 已存在，请重新输入或生成');
            die();
        }
        $array = new stdClass();
        $array->type = $type;
        $array->status = '待激活';
        $array = json_encode($array);
        $DataArr = array(
            'name' => $name,
            'utype' => $array,
            'invcode' => $invcode
        );
        $sql = $zbp->db->sql->Insert($GLOBALS['table']['plugin_oauth2_user'], $DataArr);
        $zbp->db->Insert($sql);
        SendAlertJS('用户：' . $name . ' 已创建');
        SendGetRadomString();
        break;
    case "CheckInvcode":
        CheckParamIsSet("invcode");
        echo CheckInvcode($_POST['invcode']);
        break;
    case "DelUser":
        CheckParamIsSet("uid");
        ChangeUserStatus($_POST["uid"], "已删除", "该用户已删除");
        Redirect('./main.php');
        break;
    case 'RecUser':
        CheckParamIsSet("uid");
        ChangeUserStatus($_POST["uid"], "正常", "该用户已恢复");
        Redirect('./main.php');
        break;
    case "DenUser":
        CheckParamIsSet("uid");
        ChangeUserStatus($_POST["uid"], "禁止访问", "该用户已禁止访问");
        break;
    //群组操作类
    case "NewGroup":
        CheckParamIsSet('Nid');
        $Nid = $_POST['Nid'];
        echo <<<EOF
<tr class="color2">
<td class="td5 tdCenter"></td>
<td class="td5">
<input type="text" id="NewGroupName$Nid"/>
</td>
<td class="td10">
<select size="1" id="NewGroupTemplate$Nid">
    <option value="所有阅读权限">所有阅读权限</option>
    <option value="自定义">自定义</option>
    <option value="游客" selected>游客</option>
    <option value="禁止访问">禁止访问</option>
</select>
</td>
<td class="td10 tdCenter">
    <input type="checkbox" id="NewGroupSpy$Nid" checked disabled />
    <label for="NewGroupSpy$Nid">是</label>
</td>
EOF;
        $catenum = 0;
        $catelist = new stdClass();
        foreach ($zbp->categorysbyorder as $id => $cate) {
            $catenum++;
            $catelist->$catenum = $cate->ID;
        }
        for ($num = 1; $num <= $catenum; $num++) {
            $cid = $catelist->$num;
            $ids = 'NewGroupCate' . $Nid . 'CateNum' . $num;
            echo <<<EOF
<td class="td15 tdCenter">
<input type="checkbox" id="$ids"/>
<label for="$ids">禁止</label></td>
EOF;
        }
        echo <<<EOF
<td class="td10 tdCenter">
<a href="#" class="button"><img src = "../../../zb_system/image/admin/tick.png" alt = "提交" title = "提交" width = "16" ></a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="#" class="button"><img src = "../../../zb_system/image/admin/delete.png" alt = "删除" title = "删除" width = "16" ></a>
</td></tr >
EOF;
        break;
    case "UpdateGroup":
        CheckParamIsSet("gid","gname","gtemplate","gspy");
        $catelist = new stdClass();
        $catenum = 0;
        $data = new stdClass();
        foreach ($zbp->categorysbyorder as $id => $cate) {
            $catenum++;
            $catelist->$catenum = $cate->ID;
        }
        for ($i = 1; $i <= $catenum; $i++) {
            $t = $catelist->$i;
            $data->$t = ($_POST['cl' . (string)$i] == "true" ? true : false);
        }
        $DataArr = array(
            'gname' => $_POST['gname'],
            'template' => $_POST['gtemplate'],
            'spy' => $_POST['gspy'],
            'oauth' => json_encode($data),
            'status' => "正常"
        );
        $where = array(array('=', 'gid', $_POST['gid']));
        $sql = $zbp->db->sql->Update($GLOBALS['table']['plugin_oauth2_group'], $DataArr, $where);
        $zbp->db->Update($sql);
        $json = new stdClass();
        $json->status = true;
        $json->script =GenerateHintJS("good","用户组已删除");
        $json = json_encode($json);
        echo $json;
        break;
    case "CreatGroup":
        CheckParamIsSet("gname", "gtemplate", "gspy");
        $where = array(array('=', 'gname', $_POST['gname']));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], 'gname', $where);
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
        if (!empty($array)) {
            echo '{"status":false,"reason":"该群组名称已存在"}';
            die();
        }
        $catelist = new stdClass();
        $catenum = 0;
        $data = new stdClass();
        foreach ($zbp->categorysbyorder as $id => $cate) {
            $catenum++;
            $catelist->$catenum = $cate->ID;
        }
        for ($i = 1; $i <= $catenum; $i++) {
            $t = $catelist->$i;
            $data->$t = ($_POST['cl' . (string)$i] == "true" ? true : false);
        }
        $DataArr = array(
            'gname' => $_POST['gname'],
            'template' => $_POST['gtemplate'],
            'spy' => $_POST['gspy'],
            'oauth' => json_encode($data),
            'status' => "正常"
        );
        $sql = $zbp->db->sql->Insert($GLOBALS['table']['plugin_oauth2_group'], $DataArr);
        $zbp->db->Insert($sql);
        $json = new stdClass();
        $where = array(array('=', 'gname', $_POST['gname']));
        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], 'gid', $where);
        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
        foreach ($array as $key => $reg) {
            $json->gid = $reg->gid;
        }
        $json->status = !empty($json->gid);
        $json = json_encode($json);
        echo $json;
        break;
    case "DelGroup":
        CheckParamIsSet("gid");
        $where = array(array('=', 'gid', $_POST['gid']));
        $array = array('status' => "已删除");
        $sql = $zbp->db->sql->Update($GLOBALS['table']['plugin_oauth2_group'], $array, $where);
        $zbp->db->Update($sql);
        $json = new stdClass();
        $json->status = true;
        $json->script =GenerateHintJS("good","用户组已删除");
        $json = json_encode($json);
        echo $json;
        break;
    default:
        echo "<H1>Error - Unknow command</H1>";
}


/*
if (isset($_POST["name"])) {

}
if (isset($_GET["name"])){
    print nau($_GET["name"]);
}
function nau($name){
    global $zbp;
    $where = array(array('=','name',$name));
    $sql= $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'],'*',$where);
    $array=$zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'],$GLOBALS['datainfo']['plugin_oauth2_user'],$sql);
    if (empty($array)){
        return false;
    }
    return true;
}
if (isset($_POST["invcode"])){
    print iau($_POST["invcode"]);
}
function iau($invcode){
    global $zbp;
    $where = array(array('=','invcode',$invcode));
    $sql= $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'],'*',$where);
    $array=$zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'],$GLOBALS['datainfo']['plugin_oauth2_user'],$sql);
    if (empty($array)){
        return false;
    }
    return true;
}
*/