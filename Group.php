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
$blogtitle = 'oauth2 - 分组授权';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
require './class/oauth2.php';
$oauth2 = new Oauth2();
?>
    <div id="divMain">
        <div class="divHeader"><?php echo $blogtitle; ?></div>
        <?php require dirname(__FILE__) . '/header.php'; ?>
        <div id="divMain2">
            <table border="1" class="tableFull tableBorder tableBorder-thcenter">
                <tbody>
                <!--<tr>
                    <td colspan="3" id="notice" style="display: none;">
                        <p style="color:#960;padding:0.8em;">
                            暂时为空
                        </p>
                    </td>
                </tr>-->
                <tr class="color1">
                    <th>GID</th>
                    <th>用户组</th>
                    <th>权限模板</th>
                    <th>防盗开关</th>
                    <?php
                    $catenum = 0;
                    $catelist = new stdClass();
                    foreach ($zbp->categorysbyorder as $id => $cate) {
                        $catenum++;
                        echo '<th>' . $cate->ID . "." . $cate->SymbolName . '</th>';
                        $catelist->$catenum = $cate->ID;
                    }
                    ?>
                    <th>管理</th>
                </tr>
                <?php
                $str = "";
                $array = $oauth2->GetGroupList();
                foreach ($array as $key => $reg) {
                    $gid = $reg->gid;
                    $json = json_decode($reg->oauth);
                    if ($reg->status != "已删除") {
                        $str .= <<<EOF
<tr class="color3">
<td class="td5 tdCenter">$gid</td>
<td class="td5" id="GroupName$gid">
$reg->gname
</td>
<td class="td10" id="GroupTemplate$gid">
$reg->template
<td class="td10 tdCenter">
EOF;
                        $str .= '<input type="checkbox" id="GroupSpy' . $gid . '" ' . ($reg->template != "自定义" || $reg->spy == 'checked' ? 'checked />' : "/>");
                        $str .= "\r\n";
                        $str .= '<label for="GroupSpy' . $gid . '">' . (($reg->template != "自定义" || $reg->spy) ? "是" : "否") . '</label></td>';//SPY开关 gd . $gid

                        for ($num = 1; $num <= $catenum; $num++) {
                            $cid = $catelist->$num;
                            $ids = 'GroupCate' . $gid . 'Catenum' . $num;
                            $str .= '<td class="td15 tdCenter">
<input type="checkbox" ' . ($json->$cid ? "checked" : "") . ' id="' . $ids . '"/>
<label for="' . $ids . '">' . ($json->$cid ? '允许' : "禁止") . '</label></td>';
                        }

                        $str .= '<td class="td10 tdCenter">
                            <a href="#" class="button"><img src="../../../zb_system/image/admin/page_edit.png" alt="编辑" title="编辑" width="16"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="#" class="button"><img src="../../../zb_system/image/admin/delete.png" alt="删除" title="删除" width="16"></a>
                        </td>';                                                                                         //管理工具

                        $str .= '</tr>';
                    }
                }
                echo $str;
                ?>
                <tr>
                    <td colspan="4"></td>
                    <td class="tdCenter">
                        <button class="color1 lbt" type="button">
                            新建用户组
                        </button>
                    </td>
        </div>
    </div>
    <script src="common.js" type="text/javascript"></script>
    <script type="text/javascript">
        AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");
        var cmdw = "./cmdw.php?t=" + (new Date()).getTime(),
            nid = 0;
        $("td[colspan='4']").attr("colspan", "6");
        //$("#notice").attr("colspan", $("tr.color1").children().length);
        $(":checkbox").prop("disabled", true);

        $(document).on('click', "a.button", function () {
            var GroupLine = $(this).parent().parent();
            var tmp;
            if ($(this).children().attr("alt") == "编辑") {
                $(this).children().attr({"src": "../../../zb_system/image/admin/tick.png", "alt": "保存", "title": "保存"});
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
                    '</select>').val($.trim(tmp.text())));
            } else if ($(this).children().attr("alt") == "提交" || $(this).children().attr("alt") == "保存") {
                tmp = GroupLine.children();
                var gname = tmp.eq(1).children().eq(0);
                if (gname.val() == "") {
                    vtip(gname);
                    return;
                }
                var json = {};
                if (tmp.eq(0).text() == "") {
                    json.action = "CreatGroup";
                } else {
                    json.action = "UpdateGroup";
                    json.gid = tmp.eq(0).text();
                }
                json.gname = gname.val();
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
                        showMsg(data);
                        if (!data.status) {
                            return;
                        }
                        GroupLine.fadeOut("slow", function () {
                            tmp.eq(0).html(data.gid);
                            tmp.eq(1).html(gname.val());
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
                    if (confirm('您确定要进行删除操作吗？')) {
                        $.ajax({
                            url: cmdw,
                            type: "POST",
                            data: {
                                action: "DelGroup",
                                gid: GroupLine.children().first().text()
                            },
                            dataType: "json",
                            success: function (data) {
                                showMsg(data);
                                if (data.status) {
                                    GroupLine.fadeOut("slow", function () {
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
                    }
                } else {
                    GroupLine.remove();
                }
            }
        })
        ;

        $("button.color1").last().click(function () {
            nid++;
            $.ajax({
                url: cmdw,
                type: "POST",
                data: {
                    action: "NewGroup",
                    Nid: nid
                },
                success: function (data) {
                    console.log(data);
                    $("tr:last").before(data.html);
                }
            });
        });

        $(document).on('change', "input[id^='NewGroupSpy'],input[id^='GroupSpy']", function () {
            if (this.checked) {
                $(this).next(":contains(否)").text("是");
            } else {
                $(this).next(":contains(是)").text("否");
            }
        });

        $(document).on('change', "input[id^='NewGroupCate'],input[id^='GroupCate']", function () {
            if (this.checked) {
                $(this).next(":contains(禁止)").text("允许");
            } else {
                $(this).next(":contains(允许)").text("禁止");
            }
        });

        $(document).on('change', 'select', function () {
            var checkboxs = $(this).parent().parent().find(":checkbox");
            TemplateSwitch(this.value, checkboxs);
        });

        function TemplateSwitch(String, checkboxs) {
            var Spy = checkboxs.eq(0);
            var Cate = checkboxs.slice(-checkboxs.length + 1);
            switch (String) {
                case "所有阅读权限"://所有
                    checkboxs.prop("checked", true);
                    checkboxs.prop("disabled", true);
                    checkboxs.next(":contains(否)").text("&nbsp;是");
                    checkboxs.next(":contains(禁止)").text(" 允许");
                    break;
                case "自定义"://自定义
                    Spy.prop("checked", true);
                    checkboxs.prop("disabled", false);
                    checkboxs.next(":contains(否)").text(" 是");
                    break;
                case "游客"://游客
                    Spy.prop("checked", true);
                    Spy.prop("disabled", true);
                    checkboxs.next(":contains(否)").text(" 是");
                    Cate.prop("disabled", false);
                    break;
                case "禁止访问"://禁止访问
                    Spy.prop("checked", true);
                    checkboxs.next(":contains(否)").text(" 是");
                    Cate.prop("checked", false);
                    checkboxs.prop("disabled", true);
                    checkboxs.next(":contains(允许)").text(" 禁止");
                    break;
                default:
                    alert("程序出BUG啦~！\r\n详情请见控制台");
                    console.log(String);
            }
        }
    </script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>