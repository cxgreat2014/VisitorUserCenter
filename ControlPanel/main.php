<?php
$blogpath = str_replace($_SERVER['PHP_SELF'], "", $_SERVER['SCRIPT_FILENAME']);
require $blogpath . '/zb_system/function/c_system_base.php';
require $blogpath . '/zb_system/function/c_system_admin.php';
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
require $blogpath . 'zb_system/admin/admin_header.php';
$blogtitle = 'oauth2 - 用户管理';
require $blogpath . 'zb_system/admin/admin_top.php';
require './class/vuc.php';
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
                            <a href="#" class="button"><img src="./image/page_edit.png" alt="编辑" title="编辑" width="16"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="#" class="button"><img src="./image/delete.png" alt="删除" title="删除" width="16"></a>
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
    <script src="js/common.js" type="text/javascript"></script>
    <script type="text/javascript">
        AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");
    </script>
    <script type="text/javascript" src="js/main.js"></script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>