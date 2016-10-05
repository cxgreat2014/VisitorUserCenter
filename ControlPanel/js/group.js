var cmdw = "./cmdw.php?t=" + (new Date()).getTime(),
    nid = 0;
$("td[colspan='4']").attr("colspan", $("tr.color1").children().length-1);
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