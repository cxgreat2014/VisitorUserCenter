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
if (isset($_GET["CreatNew"])) {
    $blogtitle = 'oauth2 - 新建用户';
} else {
    $blogtitle = 'oauth2 - 编辑用户';
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
    <div id="divMain">
        <div class="divHeader"><?php echo $blogtitle; ?></div>
        <div class="SubMenu">
            <?php require dirname(__FILE__) . '/class/ToolBar_top.php'; ?>
        </div>
        <div id="divMain2">
            <!--代码-->
            <?php

            ?>
            <script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.0.0.min.js"></script>
            <script type="text/javascript">
                function getRandomString() {
                    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678'; // 默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1
                    var maxPos = $chars.length;
                    var pwd = '';
                    for (i = 0; i < 6; i++) {
                        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
                    }
                    $.ajax({
                        type: "POST",
                        url: "./cmdw.php",
                        data: {invcode: pwd},
                        dataType: "text",
                        success: function (data) {
                            if (data != "1") {
                                var ic = document.getElementById("invcode");
                                ic.value = pwd;
                                return true;
                            } else {
                                getRandomString(6);
                            }
                        }
                    });
                }
                getRandomString(6);
                function js_submit() {
                    //data check
                    if ($("#invcode").val().length < 6 || $("#invcode").val().length > 32) {
                        alert("邀请码长度必须大于等于6位小于等于32位字符！");
                        return false;
                    }
                    if ($("#name").val() == "") {
                        alert("用户名不能为空！");
                        return false;
                    }
                    if ($("#name").val().length > 32) {
                        alert("用户名长度不能超过32个字符！");
                        return false;
                    }
                    $.ajax({
                        type: "POST",
                        url: "./cmdw.php",
                        data: {invcode: document.getElementById("invcode").value},
                        dataType: "text",
                        success: function (data) {
                            if (data == "1") {
                                alert("邀请码已被使用，请重新生成");
                                return false;
                                //getRandomString(6);
                            }
                            //submit
                            $.ajax({
                                type: "POST",
                                url: "./cmdw.php",
                                data: {
                                    name: $("#name").val(),
                                    type: $("#gs").val(),
                                    invcode: $("#invcode").val()
                                },
                                dataType: "script"
                            });
                        }
                    });
                }
            </script>
        </div>
    </div>
    <script
        type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>