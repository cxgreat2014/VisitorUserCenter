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
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
    <div id="divMain">
        <div class="divHeader"><?php echo $blogtitle; ?></div>
        <div class="SubMenu">
            <?php require dirname(__FILE__) . '/class/ToolBar_top.php'; ?>
        </div>
        <div id="divMain2">
            <style type="text/css">
                .lbt {
                    padding: 8px 4px;
                    cursor: pointer;
                    border: none;
                    background: #E1E1E1;
                    margin: 3px 3px 3px 7px;
                }

            </style>
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
                $gifm = new stdClass();
                $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], '*');
                $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
                foreach ($array as $key => $reg) {
                    $gid = $reg->gid;
                    $gifm->$gid = $reg->gname;
                }
                $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_user'], '*');
                $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_user'], $GLOBALS['datainfo']['plugin_oauth2_user'], $sql);
                foreach ($array as $key => $reg) {
                    $json = json_decode($reg->utype);
                    if ($json->status != "已删除") {
                        $str .= '<tr class="color3">';
                        $str .= '<td class="td5 tdCenter">' . $reg->uid . '</td>';
                        $str .= '<td class="td10">' . $reg->name . '</td>';
                        $gid = $json->type;
                        $str .= '<td class="td10">' . (empty($gid) ? $gifm->$gid : "自定义") . '</td>';
                        $str .= '<td class="td15">' . $reg->email . '</td>';
                        $str .= '<td class="td5 tdCenter">' . $reg->invcode . '</td>';
                        $where = array(array('=', 'uid', $reg->uid));
                        $order = array('time' => 'DESC');
                        $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_history'], 'time', $where, $order, null, null);
                        $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_history'], $GLOBALS['datainfo']['plugin_oauth2_history'], $sql);
                        $str .= '<td class="td20 tdCenter">' . (empty($array) ? "用户尚未登录" : date('Y-m-d H:i:s', $array[0]->time)) . '</td>';
                        $str .= '<td class="td5 tdCenter">' . $json->status . '</td>';
                        $str .= '<td class="td10 tdCenter">
                            <a href="ModMember.php?Mod&uid=' . $reg->uid . '" class="button"><img src="../../../zb_system/image/admin/page_edit.png" alt="编辑" title="编辑" width="16"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <a onclick="if(confirm(\'您确定要进行删除操作吗？\')){location.href=\'ModMember.php?Del&uid=' . $reg->uid . '\'}" class="button"><img src="../../../zb_system/image/admin/delete.png" alt="删除" title="删除" width="16"></a>
                        </td>';
                        $str .= '</tr>';
                    }
                }
                echo $str;
                ?>
                <tr>
                    <td colspan="7" class="tdCenter"></td>
                    <td class="tdCenter">
                        <button class="lbt" type="button" onclick="addnewuser()">
                            新建用户
                        </button>
                    </td>
                </tbody>
            </table>
        </div>
    </div>
    <!--<script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.0.0.min.js"></script>-->
    <script type="text/javascript">
        AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");
        var cmdw = "./cmdw.php?t=" + (new Date()).getTime();

        $(document).on('click', "a.button", function () {
            var GroupLine = $(this).parent().parent();
            var tmp;
            if ($(this).children().attr("alt") == "编辑") {
                /*$(this).children().attr({"src": "../../../zb_system/image/admin/tick.png", "alt": "保存", "title": "保存"});
                var child = GroupLine.children();
                tmp = child.eq(1);
                tmp.html('<input type="text" style="width: 100px;" value="' + $.trim(tmp.text()) + '"/>');
                tmp = child.eq(2);
                TemplateSwitch($.trim(tmp.text()), GroupLine.find(":checkbox"));

                tmp.html($('<select class="edit" style="width: 120px;" size="1" id="NewGroupTemplate$Nid">' +
                    '<option value="所有阅读权限">所有阅读权限</option>' +
                    '<option value="自定义">自定义</option>' +
                    '<option value="游客">游客</option>' +
                    '<option value="禁止访问">禁止访问</option>' +
                    '</select>').val($.trim(tmp.text())));*/
            } else if ($(this).children().attr("alt") == "提交" || $(this).children().attr("alt") == "保存") {
                tmp = GroupLine.children();
                if (tmp.eq(1).children().eq(0).val() == "") {
                    ShowTip(tmp.eq(1).children().eq(0));
                    return;
                }
                var json = {};
                if (tmp.eq(0).text() == "") {
                    json.action = "CreatUser";
                } else {
                    json.action = "UpdateUser";
                    json.gid = tmp.eq(0).text();
                }
                json.gname = tmp.eq(1).children().eq(0).val();
                json.gtemplate = tmp.eq(2).children().eq(0).val();
                json.gspy = tmp.eq(3).children().eq(0).prop("checked");
                for (var i = 4; i < (tmp.length - 1); i++) {
                    json["cl" + (i - 3)] = tmp.eq(i).children().eq(0).prop("checked");
                }
                $.ajax({
                    url: cmdw,
                    type: "POST",
                    data: json,
                    dataType: "json",
                    success: function (data) {
                        if (!data.status) {
                            alert(data.reason);
                            return;
                        }
                        GroupLine.fadeOut("slow", function () {
                            tmp.eq(0).html(data.gid);
                            tmp.eq(1).html(tmp.eq(1).children().eq(0).val());
                            tmp.eq(2).html(tmp.eq(2).children().eq(0).val());
                            GroupLine.find(":checkbox").prop("disabled", true);
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
                            action: "DelGroup",
                            gid: GroupLine.children().first().text()
                        },
                        dataType: "json",
                        success: function (data) {
                            if (data.status) {
                                GroupLine.fadeOut("slow", function () {
                                    eval(data.script);
                                    GroupLine.remove();
                                });
                            } else {
                                alert("删除失败,详情请见控制台");
                                console.log(data);
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
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

        function ShowTip(ipt) {
            $('body').append('<p id="vtip"><img id="vtipArrow" src="./image/vtip_arrow.png"/><img src="./image/inform.png" style="position: absolute;top: 12px;left: 8px;">' + "该字段您还未填写哟~" + '</p>');
            $('p#vtip').css({
                "top": (ipt.offset().top - ipt.outerHeight() - 24) + "px",
                "left": ipt.offset().left + "px",
                "padding-left": "31px",
                "padding-right": "11px"
            }).fadeIn("slow");
            setTimeout(function () {
                $('p#vtip').fadeOut("slow", function () {
                    $('p#vtip').remove();
                });
            }, 6000);
        }

        function addNewUser() {
            $('tr:last').before('<tr class="color3">' +
                '<td class="td5 tdCenter">&nbsp;</td>' +
                '<td class="td10"><input type="text" id="NewUserName"></td>' +
                '<td class="td10"><select>' +
                '<?php
                    $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], '*');
                    $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
                    foreach ($array as $key => $reg) {
                        if ($reg->status != "已删除") {
                            $gid = $reg->gid;
                            echo <<<EOF
<option value="$gid">{$reg->gname}</option>
EOF;
                        }
                    }
                    ?>' +
                '</select></td>' +
                '<td class="td15"></td>' +
                '<td class="tdCenter"><div  style="width: 107px">' +
                '<input type="text" style="width: 66px;" id="NewUserInvcode" />' +
                '<a style="cursor: pointer;"><img style="margin-bottom: -11px;margin-right: -10px;" src="./image/gencode.png" alt="重新生成邀请码" title="重新生成邀请码" onclick="getRandomString(this.parentNode.parentNode.firstChild.id)" width="32" >' +
                '</div></td>' +
                '<td class="td20 tdCenter"></td>' +
                '<td class="td5 tdCenter"><select><option value="待激活">待激活</option><option value="正常">正常</optionval></select></td>' +
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
                dataType: "text",
                success: function (data) {
                    if (data == "1") {
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