function hint(status, msg) {
    $('<div class="hint"><p class="hint hint_' + status + '">' + (msg == "" ? "操作成功完成" : msg) + '</p></div>').insertBefore($("div#divMain")).delay(3500).hide(1500, function () {
        this.remove()
    });
}

function vtip(obj, msg) {
    var top = $(obj).offset().top - $(obj).outerHeight() - 24,
        left = $(obj).offset().left, tip;
    tip = $('body').append('<p id="vtip"><img id="vtipArrow" src="./image/vtip_arrow.png"/>' +
        '<img src="./image/inform.png" style="position: absolute;top: 12px;left: 8px;">' +
        (typeof(msg) == "undefined" || msg == "" ? "该字段您还未填写哟~" : msg) + '</p>').children().last().css({
        "top": top.toString() + "px",
        "left": left.toString() + "px",
        "padding-left": "31px",
        "padding-right": "11px"
    });
    tip.children().first().attr("style","top:"+ (tip.outerHeight() -2).toString() + "px;")
    tip.fadeIn("slow", function () {
        $(this).delay(6000).fadeOut("slow", function () {
            this.remove()
        });
    });
}