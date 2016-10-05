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
        $(this).children().attr({"src": "./image/tick.png", "alt": "保存", "title": "保存"});
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

        if ($.trim(uidt.text()) == "") {
            json.action = "CreatUser";
        } else {
            json.action = "UpdateUser";
            json.uid = $.trim(uidt.text());
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
                    groupt.html(groups.find("option:selected").text());
                    invcodet.html(invcodei.val());
                    statust.html(statuss.val());
                    trline.find('img[alt="提交"],img[alt="保存"]').attr({
                        "src": "./image/page_edit.png",
                        "alt": "编辑",
                        "title": "编辑"
                    });
                    if ($.trim(child.eq(5).text()).length > 0) {
                        trline.removeAttr("style").fadeIn("slow");
                    } else {
                        trline.removeAttr("style").insertBefore($("tr:last"));
                    }
                });
            }
        });
    } else {
        if ($.trim(trline.children().first().text()) != "") {
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
        '<a href="#" class="button"><img src = "./image/tick.png" alt = "提交" title = "提交" width = "16" ></a>&nbsp;&nbsp;&nbsp;' +
        '<a href="#" class="button"><img src = "./image/delete.png" alt = "删除" title = "删除" width = "16" ></a> </td></tr >');
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