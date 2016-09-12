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

$blogtitle = 'oauth2 - 用户管理';
require '../../../zb_system/admin/admin_header.php';
require '../../../zb_system/admin/admin_top.php';
?>
    <div id="divMain">
        <div class="divHeader"><?php echo $blogtitle; ?></div>
        <div class="SubMenu">
            <?php require dirname(__FILE__) . '/header.php'; ?>
        </div>
        <div id="divMain2">
            <table border="1" class="tableFull tableBorder tableBorder-thcenter">
                <tbody>
                <tr class="color1">
                    <th>UID</th>
                    <th>用户名</th>
                    <th>用户组</th>
                    <th>邮箱</th>
                    <th>邀请码</th>
                    <th>上一次登录时间</th>
                    <th>状态</th>
                    <th>管理</th>
                </tr>
                <?php
                $str = "";
                $gifm = array();
                $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], '*');
                $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
                foreach ($array as $key => $reg) {
                    $gid = $reg->gid;
                    $gifm[$gid] = $reg->gname;
                }
                $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'], '*');
                $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql);
                foreach ($array as $key => $reg) {
                    if ($reg->status != "已删除") {
                        $json = json_decode($reg->type);
                        $str .= '<tr class="color3">';
                        $str .= '<td class="td5 tdCenter">' . $reg->uid . '</td>';
                        $str .= '<td class="td10">' . $reg->name . '</td>';
                        $str .= '<td class="td10">';
                        if ($json->type != "group") {
                            echo '自定义';
                        } else {
                            echo $gifm[$reg->gid];
                        }
                        $str .= '</td>';
                        $str .= '<td class="td15">' . $reg->email . '</td>';
                        $str .= '<td class="td5 tdCenter">' . $reg->invcode . '</td>';
                        $where = array(array('=', 'uid', $reg->uid));
                        $order = array('time' => 'DESC');
                        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_history'], 'time', $where, $order, null, null);
                        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_history'], $GLOBALS['datainfo']['plugin_oauth2_history'], $sql);
                        $str .= '<td class="td20 tdCenter">' . (empty($array) ? "用户尚未登录" : date('Y-m-d H:i:s', $array[0]->time)) . '</td>';
                        $str .= '<td class="td5 tdCenter">' . $reg->status . '</td>';
                        $str .= '<td class="td10 tdCenter">
                            <a href="#" class="button"><img src="../../../zb_system/image/admin/page_edit.png" alt="编辑" title="编辑" width="16"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="#" class="button"><img src="../../../zb_system/image/admin/delete.png" alt="删除" title="删除" width="16"></a>
                        </td>';
                        $str .= '</tr>';
                    }
                }
                echo $str;

                $groupselect = "";
                $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], '*');
                $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
                foreach ($array as $key => $reg) {
                    if ($reg->status != "已删除") {
                        $gid = $reg->gid;
                        $groupselect .= "<option value=\"$gid\">{$reg->gname}</option>";
                    }
                }
                ?>
                <tr>
                    <td colspan="7" class="tdCenter"></td>
                    <td class="tdCenter">
                        <button class="lbt" type="button" onclick="addNewUser()">
                            新建用户
                        </button>
                    </td>
                </tbody>
            </table>
        </div>
    </div>
    <!--<script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.0.0.min.js"></script>-->
    <script src="common.js" type="text/javascript"></script>
    <script type="text/javascript">
        AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");
        var cmdw = "./cmdw.php?t=" + (new Date()).getTime();
        var newnum,
            nameinput = '<input type="text" style="width: 100px;" />',
            groupselect = '<select><?php echo $groupselect;?><option value="自定义">自定义</option></select>',
            invinput = '<div  style="width: 107px"><input type="text" style="width: 66px;" id="NewUserInvcode" />' +
                '<a style="cursor: pointer;"><img style="margin-bottom: -11px;margin-right: -10px;" src="./image/gencode.png" ' +
                'alt="重新生成邀请码" title="重新生成邀请码" onclick="getRandomString(this.parentNode.parentNode.firstChild.id)" width="32" ></div>',
            statusselect = '<select><option value="待激活">待激活</option><option value="正常">正常</optionval></select>';

        $(document).on('click', "a.button", function () {
            var GroupLine = $(this).parent().parent();
            var child = GroupLine.children();
            var uid = child.eq(0),
                name = child.eq(1),
                group = child.eq(2),
                invcode = child.eq(4),
                status = child.eq(6);
            if ($(this).children().attr("alt") == "编辑") {
                $(this).children().attr({"src": "../../../zb_system/image/admin/tick.png", "alt": "保存", "title": "保存"});
                name.html($(nameinput).val($.trim(name.text())));
                group.html($(groupselect).val($.trim(group.text())));
                invcode.html($(invinput).children().first().val($.trim(invcode.text())));
                status.html($(statusselect).val($.trim(status.text())));
            } else if ($(this).children().attr("alt") == "提交" || $(this).children().attr("alt") == "保存") {
                var namei = child.eq(1).children().eq(0),
                    groups = child.eq(2).children().eq(0),
                    invcodei = child.eq(4).children().children().eq(0),
                    statuss = child.eq(6).children().eq(0);
                var json = {};
                json.name = namei.val();
                if (json.name == "") {
                    vtip(namei);
                    return;
                }
                json.invcode = invcodei.val();
                console.log(invcodei.val());
                if (json.invcode == "") {
                    vtip(invcodei);
                    return;
                }

                if (uid.text() == "") {
                    json.action = "CreatUser";
                } else {
                    json.action = "UpdateUser";
                    json.uid = uid.text();
                }

                if (groups.val() == "自定义") {
                    json.type = "自定义";
                    json.gid = 0;
                } else {
                    json.type = "group";
                    json.gid = groups.val();
                }
                json.status = statuss.val();
                json.action="CreatUser";
                $.ajax({
                    url: cmdw,
                    type: "POST",
                    data: json,
                    dataType: "json",
                    success: function (data) {
                        if (!data.status) {
                            switch (data.action) {
                                case "hint":
                                    hint(json.hint[0].status, json.hint[0].msg);
                                    break;
                                case "vtip":
                                    vtip(eval(json.vtip[0].place), json.vtip[0].msg);
                                    break;
                                case "alert":
                                    alert(json.msg.msg);
                                    break;
                                default:
                                    alert("程序出错啦!详见控制台");
                                    console.log(json);
                            }
                            return;
                        }
                        GroupLine.fadeOut("slow", function () {
                            uid.html(data.uid);
                            name.html(namei.val());
                            group.html(groups.val());
                            invcode.html(invcodei.val());
                            status.html(statuss.val());
                            GroupLine.find('img[alt="提交"],img[alt="保存"]').attr({
                                "src": "../../../zb_system/image/admin/page_edit.png",
                                "alt": "编辑",
                                "title": "编辑"
                            });
                            if (GroupLine.find('[id^="GroupName"]').length > 0) {
                                GroupLine.removeAttr("style").fadeIn("slow");
                            } else {
                                GroupLine.removeAttr("style").insertBefore($("tr:last"));
                            }
                        });
                    }
                });
            } else {
                if (GroupLine.children().first().text() != "") {
                    $.ajax({
                        url: cmdw,
                        type: "POST",
                        data: {
                            action: "DelUser",
                            uid: uid.text()
                        },
                        dataType: "json",
                        success: function (data) {
                            if (data.status) {
                                GroupLine.fadeOut("slow", function () {
                                    hint(data.hint.status,data.hint.msg);
                                    GroupLine.remove();
                                });
                            } else {
                                alert("删除失败,详情请见控制台");
                                console.log(data);
                            }
                        },
                        error: function (XMLHttpRequest, textStatus) {
                            alert("删除请求失败,详情请见控制台");
                            console.log(XMLHttpRequest.status);
                            console.log(XMLHttpRequest.readyState);
                            console.log(textStatus);
                        }
                    })
                } else {
                    GroupLine.remove();
                }
            }
        });

        function addNewUser() {
            newnum++;
            $('tr:last').before('<tr class="color3">' +
                '<td class="td5 tdCenter">&nbsp;</td>' +
                '<td class="td10">' + nameinput + '</td>' +
                '<td class="td10">' + groupselect + '</td>' +
                '<td class="td15"></td>' +
                '<td class="tdCenter">' +
                invinput + '</td>' +
                '<td class="td20 tdCenter"></td>' +
                '<td class="td5 tdCenter">' + statusselect +
                '<td class="td10 tdCenter">' +
                '<a href="#" class="button"><img src = "../../../zb_system/image/admin/tick.png" alt = "提交" title = "提交" width = "16" ></a>&nbsp;&nbsp;&nbsp;' +
                '<a href="#" class="button"><img src = "../../../zb_system/image/admin/delete.png" alt = "删除" title = "删除" width = "16" ></a> </td></tr >');
            if (newnum > 1) {
                $("[colspan='7']").html('<button class="lbt" type="button"> 提交全部 </button>');
            }
            getRandomString('NewUserInvcode' + newnum.toString());
        }


        function getRandomString(id) {
            var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678'; // 默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1
            var maxPos = $chars.length;
            var Invcode = '';
            for (var i = 0; i < 6; i++) {
                Invcode += $chars.charAt(Math.floor(Math.random() * maxPos));
            }
            $.ajax({
                type: "POST",
                url: "./cmdw.php",
                data: {
                    action: "CheckInvcode",
                    invcode: Invcode
                },
                dataType: "json",
                success: function (data) {
                    if (data.status) {
                        document.getElementById(id).value = Invcode;
                        return true;
                    } else {
                        getRandomString(id);
                    }
                }
            });
        }
    </script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>