<?php
define('UC_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "\\1", str_replace('\\', '/', __FILE__)));
define('site_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "", str_replace('\\', '/', __FILE__)) . "/");
require_once UC_path . 'ControlPanel/main.php';
$blogtitle = 'VisitorUserCenter - 用户管理';
global $zbp;
global $vuc;
require dirname(__FILE__) . '/header.php';
?>
    <div class="divHeader"><?php echo $blogtitle; ?></div>
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
            $array = $vuc->GetGroupList();
            foreach ($array as $key => $reg) {
                $gid = $reg->gid;
                $gifm[$gid] = $reg->gname;
                if ($reg->status != "已删除") {
                    $groupselect .= "<option value=\"$gid\">{$reg->gname}</option>";
                }
            }
            $array = $vuc->GetUserList();
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
                    $array = $vuc->GetUserLastLogin($reg->uid);
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
    <script type="text/javascript" src="js/main.js"></script>
<?php require UC_path . 'ControlPanel/footer.php'; ?>