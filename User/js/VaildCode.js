var handlerEmbed = function (captchaObj) {
    $("#embed-submit").click(function (e) {
        if ($("#user").val() === "" || $("#pwd").val() === "") {
            return;
        }
        e.preventDefault();
        var validate = captchaObj.getValidate();
        if (!validate) {
            $("#notice")[0].className = "show";
            setTimeout(function () {
                    $("#notice")[0].className = "hide";
                },
                2000);
            return;
        } else {
            $.ajax({
                url: "../class/VerifyUserToken.php?t=" + (new Date()).getTime(),
                type: "POST",
                data: {
                    user: $("#user").val(),
                    pwd: $("#pwd").val(),
                    geetest_challenge: $(".geetest_challenge").val(),
                    geetest_validate: $(".geetest_validate").val(),
                    geetest_seccode: $(".geetest_seccode").val()
                },
                dataType: "script"
            });
        }
    });
    // 将验证码加到id为captcha的元素里
    captchaObj.appendTo("#embed-captcha");
    captchaObj.onReady(function () {
        $("#wait")[0].className = "hide";
    });
    // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
};
$.ajax({
    // 获取id，challenge，success（是否启用failback）
    url: "../class/VerifyUserToken.php?t=" + (new Date()).getTime(),
    // 加随机数防止缓存
    type: "get",
    dataType: "json",
    success: function (data) {
        // 使用initGeetest接口
        // 参数1：配置参数
        // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
        initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                product: "embed",
                // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                offline: !data.success // 表示用户后台检测极验服务器是否宕机，一般不需要关注
            },
            handlerEmbed);
    }
});