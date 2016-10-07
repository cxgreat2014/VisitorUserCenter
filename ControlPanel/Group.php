<?php
define('UC_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "\\1", str_replace('\\', '/', __FILE__)));
define('site_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "", str_replace('\\', '/', __FILE__)) . "/");
require_once UC_path . 'ControlPanel/main.php';
$blogtitle = 'VisitorUserCenter - 分组授权';
global $zbp;
global $vuc;
require dirname(__FILE__) . '/header.php';
?>
<div class="divHeader"><?php echo $blogtitle; ?></div>
<div id="divMain2">
    <table border="1" class="tableFull tableBorder tableBorder-thcenter">
        <tbody><?php
        /*        <!--<tr><td colspan="3" id="notice" style="display: none;"><p style="color:#960;padding:0.8em;">暂时为空</p></td></tr>-->*/?>
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
        $array = $vuc->GetGroupList();
        foreach ($array as $key => $reg) {
            $gid = $reg->gid;
            $json = json_decode($reg->oauth);
            if ($reg->status != "已删除") {
                $str .= <<<EOF
<tr class="color3">
<td class="td5 tdCenter">$gid</td>
<td sytle="width:6%;" id="GroupName$gid">
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
                <button class="color1 lbt" type="button">
                    新建用户组
                </button>
            </td>
        </tr>
        </tbody>
        </table>
</div>
<script type="text/javascript" src="js/group.js"></script>
<?php require UC_path . 'ControlPanel/footer.php'; ?>

