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
require './class/oauth2.php';
$oauth2 = new Oauth2();
$blogtitle = 'oauth2 - 记录查询';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
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
                    <th>日志ID</th>
                    <th>用户ID</th>
                    <th>用户名</th>
                    <th>记录时间</th>
                    <th>消息类型</th>
                    <th>消息内容</th>
                </tr>
                <tr class="color3">
                    <?php
                    $str = "";
                    $array = $oauth2->GetHistoryList();
                    foreach ($array as $key => $reg) {
                        $str .= '<tr><td class="td5 tdCenter">' . $reg->logid . '</td>';
                        $str .= '<td class="td5">' . $reg->uid . '</td>';
                        $str .= '<td class="td10">' . $oauth2->GetUserByUid($reg->uid)[0]->name . '</td>';
                        $str .= '<td>' . $reg->time . '</td>';
                        $str .= '<td>' . $reg->logtype . '</td>';
                        $str .= '<td>' . $reg->logmsg . '</td>';
                        $str .= '</tr>';
                    }
                    echo $str;
                    ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script
        type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>