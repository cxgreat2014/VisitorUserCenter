<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
require './class/oauth2.php';
require './class/JsonReply.php';
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
$oauth2 = new Oauth2();
function CheckParamIsSet() {
    $total = 0;
    $args = func_get_args();
    for ($i = 0; $i < count($args); $i++) {
        if (is_string($args[$i])) {
            if (!isset($_POST[$args[$i]]) || $_POST[$args[$i]] == "") {
                echo '{"status":false,"vtip":"Post ' . $args[$i] . ' Error"}';
                die();
            } elseif (preg_match("/[\'.,:;*?~`!#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $_POST[$args[$i]])) {
                echo '{"status":false,"vtip":"Post ' . $args[$i] . ' Error. It Has Special Word"}';
                die();
            }
        }
    }
    return;
}

$jrp = new JsonReply();

header('Content-type: application/json');
CheckParamIsSet("action");
switch ($_POST['action']) {
    //用户操作类
    case "CreatUser":
        CheckParamIsSet("name", "type", "gid", "status", "invcode");
        $name = $_POST['name'];
        $type = $_POST['type'];
        $gid = $_POST['gid'];
        $status = $_POST['status'];
        $invcode = $_POST['invcode'];
        $jrp->ChangeStatus(false);
        if (strlen($name) > 32) {
            $jrp->SetVtip("uname", "用户名最长32个字符，请重新输入");
        } elseif (!$oauth2->CheckUserName($name)) {
            $jrp->SetVtip("uname", '用户：' . $name . " 已存在，请重新输入");
        } elseif (strlen($invcode) != 6) {
            $jrp->SetVtip("invcode", '邀请码长度必须为6，请重新输入或生成');
        } elseif (!$oauth2->CheckInvcode($invcode)) {
            $jrp->SetVtip("invcode", '邀请码：' . $invcode . ' 已存在，请重新输入或生成');
        }
        $array = new stdClass();
        if ($type == "自定义") {
            $array->type = "自定义";
            $gid = 0;
        } else {
            $array->type = "group";
            if ($gid == 0) {
                $jrp->SetVtip("type", '数据类型错误');
            }
        }
        if (!($status != "正常" || $status != "未激活")) {
            $jrp->SetVtip("status", '数据类型错误');
        }
        if (!empty($jrp->json['vtip'])) {
            $jrp->SendJsonWithDie();
        }
        $array = json_encode($array);
        $DataArr = array(
            'name' => $name,
            'type' => $array,
            'gid' => $gid,
            'invcode' => $invcode,
            'status' => $status
        );
        $oauth2->CreatUser($DataArr);
        $array = $oauth2->GetUserByName($name);
        if (empty($array)) {
            $jrp->SetAlert("在创建用户：$name 时 发生未知错误", "Can't find $name in database");
        } else {
            $jrp->ChangeStatus(true);
            $jrp->json['uid'] = $array[0]->uid;
            $jrp->SetHint('good', "用户：$name 已创建");
        }
        $jrp->SendJsonWithDie();
        break;
    case "UpdateUser":
        CheckParamIsSet("uid", "name", 'type', 'gid', 'status');
        $uid = $_POST['uid'];
        $name = $_POST['name'];
        $type = $_POST['type'];
        $gid = $_POST['gid'];
        $status = $_POST['status'];
        $invcode = $_POST['invcode'];

        $jrp->ChangeStatus(false);
        if (strlen($name) > 32) {
            $jrp->SetVtip("uname", "用户名最长32个字符，请重新输入");
        }
        $array = $oauth2->GetUserByName($name);
        if (count($array) > 0 && $array[0]->uid != $uid) {
            $jrp->SetVtip("uname", '用户：' . $name . " 已存在，请重新输入");
        } elseif (strlen($invcode) != 6) {
            $jrp->SetVtip("invcode", '邀请码长度必须为6，请重新输入或生成');
        }
        $array = $oauth2->GetUserByInvcode($invcode);
        if (count($array) > 0 && $array[0]->uid != $uid) {
            $jrp->SetVtip("invcode", '邀请码：' . $invcode . ' 已存在，请重新输入或生成');
        }
        $array = new stdClass();
        if ($type == "自定义") {
            $array->type = "自定义";
            $gid = 0;
        } else {
            $array->type = "group";
            if ($gid == 0) {
                $jrp->SetVtip("type", '数据错误');
            }
        }
        if (!($status != "正常" || $status != "未激活")) {
            $jrp->SetVtip("status", '数据错误');
        }
        if (!empty($jrp->json['vtip'])) {
            $jrp->SendJsonWithDie();
        }
        $array = json_encode($array);
        $DataArr = array(
            'name' => $name,
            'type' => $array,
            'gid' => $gid,
            'invcode' => $invcode,
            'status' => $status
        );
        $oauth2->UpdateUser($uid, $DataArr);
        if ($oauth2->CheckUserName($name)) {
            $jrp->ChangeStatus(false);
            $jrp->SetAlert("ERROR!\r\n在更新数据过程中发生致命错误!", "Can't find $name in Database");
        } else {
            $jrp->ChangeStatus(true);
            $jrp->SetHint("good", "用户 $name 的信息已更新完成");
        }
        $jrp->SendJsonWithDie();
        break;
    case "CheckInvcode":
        CheckParamIsSet("invcode");
        $jrp->ChangeStatus($oauth2->CheckInvcode($_POST['invcode']));
        $jrp->SendJsonWithDie();
        break;
    case "DelUser":
        CheckParamIsSet("uid");
        $oauth2->ChangeUserStatus($_POST["uid"], "已删除", "该用户已删除");
        break;
    case 'RecUser':
        CheckParamIsSet("uid");
        $oauth2->ChangeUserStatus($_POST["uid"], "正常", "该用户已恢复");
        break;
    case "DenUser":
        CheckParamIsSet("uid");
        $oauth2->ChangeUserStatus($_POST["uid"], "禁止访问", "该用户已禁止访问");
        break;
    //群组操作类
    case "NewGroup":
        CheckParamIsSet('Nid');
        $Nid = $_POST['Nid'];
        $str = "";
        $str .= <<<EOF
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
            $str .= <<<EOF
<td class="td15 tdCenter">
<input type="checkbox" id="$ids"/>
<label for="$ids">禁止</label></td>
EOF;
        }
        $str .= <<<EOF
<td class="td10 tdCenter">
<a href="#" class="button"><img src = "../../../zb_system/image/admin/tick.png" alt = "提交" title = "提交" width = "16" ></a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="#" class="button"><img src = "../../../zb_system/image/admin/delete.png" alt = "删除" title = "删除" width = "16" ></a>
</td></tr >
EOF;
        $jrp->ChangeStatus(true);
        $jrp->json["html"] = $str;
        $jrp->SendJsonWithDie();
        break;
    case "UpdateGroup":
        CheckParamIsSet("gid", "gname", "gtemplate", "gspy");
        $array = $oauth2->GetGroupByName($_POST['gname']);
        if (count($array) > 0 && $array[0]->gid != $_POST['gid']) {
            $jrp->ChangeStatus(false);
            $jrp->SetVtip("gname", "该用户组名已存在");
            $jrp->SendJsonWithDie();
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
        $spy=$_POST['gspy'];
        if($spy=='true'){
            $spy="1";
        }else{
            $spy="0";
        }
        $DataArr = array(
            'gname' => $_POST['gname'],
            'template' => $_POST['gtemplate'],
            'spy' => $spy,
            'oauth' => json_encode($data),
            'status' => "正常"
        );
        $oauth2->UpdateGroupInfo($_POST['gid'], $DataArr);
        $jrp->ChangeStatus(true);
        $jrp->SetHint("good", "修改已保存");
        $jrp->SendJsonWithDie();
        break;
    case "CreatGroup":
        CheckParamIsSet("gname", "gtemplate", "gspy");
        $array = $oauth2->GetGroupByName($_POST['gname']);
        if (!empty($array)) {
            $jrp->ChangeStatus(false);
            $jrp->SetVtip("gname", "该群组名称已存在");
            $jrp->SendJsonWithDie();
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
        $spy=$_POST['gspy'];
        if($spy=='true'){
            $spy="1";
        }else{
            $spy="0";
        }
        $DataArr = array(
            'gname' => $_POST['gname'],
            'template' => $_POST['gtemplate'],
            'spy' =>  $spy,
            'oauth' => json_encode($data),
            'status' => "正常"
        );
        $oauth2->CreatGroup($DataArr);
        $array = $oauth2->GetGroupByName($_POST['gname']);
        $jrp->ChangeStatus(!empty($array));
        $jrp->SetHint("good", "用户组 " . $_POST['gname'] . "已创建");
        $jrp->json['gid'] = $array[0]->gid;
        $jrp->SendJsonWithDie();
        break;
    case "DelGroup":
        CheckParamIsSet("gid");
        $array = array('status' => "已删除");
        $oauth2->UpdateGroupInfo($_POST['gid'], $array);
        $jrp->ChangeStatus(true);
        $jrp->SetHint("good", "用户组已删除");
        $jrp->SendJsonWithDie();
        break;
    default:
        $jrp->ChangeStatus(false);
        $jrp->SetAlert("未知命令","Unknow Command;");
        $jrp->SendJsonWithDie();
}