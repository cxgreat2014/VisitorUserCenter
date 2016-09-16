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
require './class/oauth2.php';
$oauth2 = new Oauth2();
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
                $groupselect = "";
                $gifm = array();
                $array = $oauth2->GetGroupList();
                foreach ($array as $key => $reg) {
                    $gid = $reg->gid;
                    $gifm[$gid] = $reg->gname;
                    if ($reg->status != "已删除") {
                        $groupselect .= "<option value=\"$gid\">{$reg->gname}</option>";
                    }
                }
                $array = $oauth2->GetUserList();
                foreach ($array as $key => $reg) {
                    if ($reg->status != "已删除") {
                        $json = json_decode($reg->type);
                        $str .= '<tr class="color3">';
                        $str .= '<td class="td5 tdCenter">' . $reg->uid . '</td>';
                        $str .= '<td class="td10">' . $reg->name . '</td>';
                        $str .= '<td class="td10">';
                        if ($json->type != "group") {
                            $str .= '自定义';
                        } else {
                            $str .= $gifm[$reg->gid];
                        }
                        $str .= '</td>';
                        $str .= '<td class="td15">' . $reg->email . '</td>';
                        $str .= '<td class="td5 tdCenter">' . $reg->invcode . '</td>';
                        $array = $oauth2->GetUserLastLogin($reg->uid);
                        $str .= '<td class="td20 tdCenter">' . (empty($array) ? "用户尚未登录" : $array[0]->time) . '</td>';
                        $str .= '<td class="tdCenter" style="width: 6%;">' . $reg->status . '</td>';
                        $str .= '<td class="td10 tdCenter">
                            <a href="#" class="button"><img src="../../../zb_system/image/admin/page_edit.png" alt="编辑" title="编辑" width="16"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="#" class="button"><img src="../../../zb_system/image/admin/delete.png" alt="删除" title="删除" width="16"></a>
                        </td>';
                        $str .= '</tr>';
                    }
                }
                echo $str;
                ?>
                <tr>
                    <td colspan="7" class="tdCenter"></td>
                    <td class="tdCenter">
                        <button class="lbt" type="button">
                            新建用户
                        </button>
                    </td>
                </tbody>
            </table>
        </div>
    </div>
    <script src="common.js" type="text/javascript"></script>
    <script type="text/javascript">
        AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");
        var cmdw = "./cmdw.php?t=" + (new Date()).getTime();
        var newnum,
            nameinput = '<input type="text" style="width: 100px;" />',
            groupselect = '<select><?php echo $groupselect;?><option value="自定义">自定义</option></select>',
            invinput = '<div  style="width: 107px"><input type="text" maxlength="6" style="width: 66px;"/>' +
                '<a style="cursor: pointer;"><img style="margin-bottom: -11px;margin-right: -10px;" src="./image/gencode.png" ' +
                'alt="重新生成邀请码" title="重新生成邀请码" width="32" ></div>',
            statusselect = '<select style="width: 72px"><option value="待激活">待激活</option><option value="正常">正常</optionval></select>';
        $(document).on('click', "a>img[alt='重新生成邀请码']", function () {
            getRandomString($(this).parent().parent().children().first());
        });


        var uname, group, invcode, status;
        $(document).on('click', "a.button", function () {
            var trline = $(this).parent().parent();
            var child = trline.children();
            var uidt = child.eq(0),
                namet = child.eq(1),
                groupt = child.eq(2),
                invcodet = child.eq(4),
                statust = child.eq(6);
            if ($(this).children().attr("alt") == "编辑") {
                $(this).children().attr({"src": "../../../zb_system/image/admin/tick.png", "alt": "保存", "title": "保存"});
                namet.html($(nameinput).val($.trim(namet.text())));
                groupt.html($(groupselect).val($.trim(groupt.text())));
                var invhtml = $(invinput);
                invhtml.children().first().val($.trim(invcodet.text()));
                invcodet.html(invhtml);
                statust.html($(statusselect).val($.trim(statust.text())));
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
                if (json.invcode == "") {
                    vtip(invcodei);
                    return;
                }

                if (uidt.text() == "") {
                    json.action = "CreatUser";
                } else {
                    json.action = "UpdateUser";
                    json.uid = uidt.text();
                }

                if (groups.val() == "自定义") {
                    json.type = "自定义";
                    json.gid = 0;
                } else {
                    json.type = 'group';
                    json.gid = groups.val();
                }
                json.status = statuss.val();
                $.ajax({
                    url: cmdw,
                    type: "POST",
                    data: json,
                    dataType: "json",
                    success: function (data) {
                        uname = namei;
                        group = groups;
                        invcode = invcodei;
                        status = statuss;
                        showMsg(data);
                        if (!data.status) {
                            return;
                        }
                        trline.fadeOut("slow", function () {
                            uidt.html(data.uid);
                            namet.html(namei.val());
                            groupt.html(groups.val());
                            invcodet.html(invcodei.val());
                            statust.html(statuss.val());
                            trline.find('img[alt="提交"],img[alt="保存"]').attr({
                                "src": "../../../zb_system/image/admin/page_edit.png",
                                "alt": "编辑",
                                "title": "编辑"
                            });
                            if (child.eq(5).text().length > 0) {
                                trline.removeAttr("style").fadeIn("slow");
                            } else {
                                trline.removeAttr("style").insertBefore($("tr:last"));
                            }
                        });
                    }
                });
            } else {
                if (trline.children().first().text() != "") {
                    $.ajax({
                        url: cmdw,
                        type: "POST",
                        data: {
                            action: "DelUser",
                            uid: uidt.text()
                        },
                        dataType: "json",
                        success: function (data) {
                            showMsg(data);
                            if (data.status) {
                                trline.fadeOut("slow", function () {
                                    trline.remove();
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
                    trline.remove();
                }
            }
        });

        $("tr:last").find("button").eq(0).click(function () {
            var jnode = $('<tr class="color3">' +
                '<td class="td5 tdCenter">&nbsp;</td>' +
                '<td class="td10">' + nameinput + '</td>' +
                '<td class="td10">' + groupselect + '</td>' +
                '<td class="td15"></td>' +
                '<td class="tdCenter">' +
                invinput + '</td>' +
                '<td class="td20 tdCenter"></td>' +
                '<td class="tdCenter" style="width: 6%;">' + statusselect +
                '<td class="td10 tdCenter">' +
                '<a href="#" class="button"><img src = "../../../zb_system/image/admin/tick.png" alt = "提交" title = "提交" width = "16" ></a>&nbsp;&nbsp;&nbsp;' +
                '<a href="#" class="button"><img src = "../../../zb_system/image/admin/delete.png" alt = "删除" title = "删除" width = "16" ></a> </td></tr >');
            getRandomString(jnode.children().eq(4).children().children().eq(0));
            newnum = jnode;
            $('tr:last').before(jnode);
            if (newnum > 1) {
                $("[colspan='7']").html('<button class="lbt" type="button"> 提交全部 </button>');
            }
        });


        function getRandomString(ipt) {
            var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678'; // 默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1
            var maxPos = $chars.length;
            var Invcode = '';
            for (var i = 0; i < 6; i++) {
                Invcode += $chars.charAt(Math.floor(Math.random() * maxPos));
            }
            $.ajax({
                type: "POST",
                url: cmdw,
                data: {
                    action: "CheckInvcode",
                    invcode: Invcode
                },
                dataType: "json",
                success: function (data) {
                    if (data.status) {
                        $(ipt).val(Invcode);
                    } else {
                        getRandomString(ipt);
                    }
                }
            });
        }
    </script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>