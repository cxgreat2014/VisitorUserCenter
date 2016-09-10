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
?>
    <style>
        input[type=checkbox]:focus, select:focus, a:focus {
            outline: 0;
            border-color: rgba(82, 168, 236, 0.8);
            -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 0 8px rgba(82, 168, 236, 0.6); /* Safari 4 */
            -moz-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 0 8px rgba(82, 168, 236, 0.6); /* Firefox 3.6 */
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 0 8px rgba(82, 168, 236, 0.6);
        }
        tr:hover{
            background: #ffffdd;
        }
        label {
            -moz-user-select: none; /*火狐*/
            -webkit-user-select: none; /*webkit浏览器*/
            -ms-user-select: none; /*IE10*/
            -khtml-user-select: none; /*早期浏览器*/
            user-select: none;
        }
        tr td:nth-child(3){
            padding-left: 10px;
        }
        p#vtip {
            display: none;
            position: absolute;
            padding: 10px;
            font-size: 0.8em;
            background-color: white;
            border: 1px solid #a6c9e2;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            z-index: 9999
        }

        p#vtip #vtipArrow {
            position: absolute;
            top: 39px;
            left: 5px;
            transform: rotate(180deg);
        }

        input[type="text"] {
            width: 100px;
        }

        select {
            width: 120px;
        }
    </style>
    <div id="divMain">
        <div class="divHeader"><?php echo $blogtitle; ?></div>
        <?php require dirname(__FILE__) . '/class/ToolBar_top.php'; ?>
        <div id="divMain2">
            <table border="1" class="tableFull tableBorder tableBorder-thcenter">
                <tbody>
                <tr>
                    <td colspan="3" id="notice">
                        <p style="color:#960;padding:0.8em;">
                            暂时为空
                        </p>
                    </td>
                </tr>
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
                $sql = $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_group'], '*');
                $array = $zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_group'], $GLOBALS['datainfo']['plugin_oauth2_group'], $sql);
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
                        <button class="color1" type="button"
                                style="padding:8px 4px;cursor: pointer;border: none;background:#E1E1E1;margin: 3px 3px 3px 7px;">
                            新建用户组
                        </button>
                    </td>
        </div>
    </div>
    <!--<script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.0.0.min.js"></script>-->
    <script type="text/javascript">
        AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");
        var cmdw = "./cmdw.php?t=" + (new Date()).getTime(),
            nid = 0;
        $("td[colspan='4']").attr("colspan", "6");
        $("#notice").attr("colspan", "7");
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
                if (tmp.eq(1).children().eq(0).val() == "") {
                    ShowTip(tmp.eq(1).children().eq(0));
                    return;
                }
                var json = {};
                if (tmp.eq(0).text() == "") {
                    json.action = "CreatGroup";
                } else {
                    json.action = "UpdateGroup";
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
                            });if(GroupLine.find('[id^="GroupName"]').length>0){
                                GroupLine.removeAttr("style").fadeIn("slow");
                            }else {
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
        })
        ;
        function ShowTip(ipt) {
            $('body').append('<p id="vtip"><img id="vtipArrow" src="./image/vtip_arrow.png"/><img src="./image/inform.png" style="position: absolute;top: 12px;left: 8px;">' + "该字段您还未填写哟~" + '</p>');
            $('p#vtip').css({
                "top": (ipt.offset().top - ipt.outerHeight() - 24) + "px",
                "left": ipt.offset().left + "px",
                "padding-left": "31px",
                "padding-right":"11px"
            }).fadeIn("slow");
            setTimeout(function () {
                $('p#vtip').fadeOut("slow", function () {
                    $('p#vtip').remove();
                });
            }, 6000);
        }

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
                    $("tr:last").before(data);
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
                    alert("程序出BUG啦~！\r\n快联系我修复:\r\nQQ:1437826301\r\nE-Mail:cxgreat2014@163.com");
                    console.log(String);
            }
        }
    </script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>