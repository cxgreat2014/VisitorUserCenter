<?php
define('UC_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "\\1", str_replace('\\', '/', __FILE__)));
define('site_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "", str_replace('\\', '/', __FILE__)) . "/");
require_once UC_path . 'ControlPanel/main.php';
$blogtitle = 'VisitorUserCenter - 记录查询';
global $zbp;
global $vuc;
require dirname(__FILE__) . '/header.php';
?>
    <div class="divHeader"><?php echo $blogtitle; ?></div>
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
                <th>管理</th>
            </tr>
            <tr class="color3">
                <?php
                $str = "";
                $array = $vuc->GetHistoryList();
                foreach ($array as $key => $reg) {
                    if ($reg->type == "正常") {
                        $str .= '<tr><td class="td5 tdCenter">' . $reg->logid . '</td>';
                        $str .= '<td class="td5">' . $reg->uid . '</td>';
                        $str .= '<td class="td10">' . ($reg->uid == '0' ? 'system' : $vuc->GetUserByUid($reg->uid)[0]->name) . '</td>';
                        $str .= '<td>' . $reg->time . '</td>';
                        $str .= '<td>' . $reg->logmod . '</td>';
                        $str .= '<td>' . $reg->logmsg . '</td>';
                        $str .= '<td><a href="#" class="button"><img src="../../../zb_system/image/admin/delete.png" alt="删除" title="删除" width="16"></a></td>';
                        $str .= '</tr>';
                    }
                }
                echo $str;
                ?>
            </tr>
            </tbody>
        </table>
    </div>
<?php require UC_path . 'ControlPanel/footer.php'; ?>