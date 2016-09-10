<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('oauth2')) {$zbp->ShowError(48);die();}

$blogtitle='oauth2 - 记录查询';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
    <div id="divMain">
        <div class="divHeader"><?php echo $blogtitle;?></div>
        <div class="SubMenu">
            <?php require dirname(__FILE__).'/class/ToolBar_top.php';?>
        </div>
        <div id="divMain2">
            <table border="1" class="tableFull tableBorder tableBorder-thcenter"><tbody>
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
                    $where = '';//array(array('=','sean_Type','0'));
                    $order = '';array('sean_IsUsed'=>'DESC','sean_Order'=>'ASC');
                    $sql= $zbp->db->sql->Select($GLOBALS['table']['plugin_oauth2_history'],'*',$where,$order,null,null);
                    $array=$zbp->GetListCustom($GLOBALS['table']['plugin_oauth2_history'],$GLOBALS['datainfo']['plugin_oauth2_history'],$sql);
                    foreach ($array as $key => $reg) {
                        $str .= '<td class="td5 tdCenter">'.$reg->logid.'</td>';
                        $str .= '<td class="td5">'.$reg->uid.'</td>';
                        $str .= '<td class="td10">'.$i.'</td>';
                        $str .= '<td><input type="hidden" name="title" value="'.$reg->Title.'" >'.$reg->Title.'</td>';
                        $str .= '<td><input  type="hidden"  name="img"  value="'.$reg->Img.'" /><img src="'.$reg->Img.'" width="190" height="120" border="0"></td>';
                        $str .= '<td><input type="hidden" name="url" value="'.$reg->Url.'" >'.$reg->Url.'</td>';
                        $str .= '<td><div class="evo-colorind" style="background-color:'.$reg->Code.'"></div></td>';
                        $str .= '<td><input type="text" class="sedit" name="order" value="'.$reg->Order.'" style="width:40px"></td>';
                        $str .= '<td><input type="text" class="checkbox" name="IsUsed" value="'.$reg->IsUsed.'" /></td>';
                        $str .= '<td nowrap="nowrap">
                        <input type="hidden" name="editid" value="'.$reg->ID.'">
                        <input name="edit" type="submit" class="button" value="修改"/>
                        <input name="del" type="button" class="button" value="删除" onclick="if(confirm(\'您确定要进行删除操作吗？\')){location.href=\'save.php?type=flashdel&id='.$reg->ID.'\'}"/>
                    </td>';
                        $str .= '</tr>';
                        $str .= '</form>';
                        /*                    <td class="td5 tdCenter">1</td>
                    <td class="td10">admin</td>
                    <td class="td10">admin</td>
                    <td>admin</td>
                    <td class="td15">admin@<?php echo $_SERVER['SERVER_NAME']; ?></td>
                    <td class="td20 tdCenter"><?php echo date('Y-m-d h:i:s',time()); ?></td>
                    <td class="td5">公开</td>*/
                    }
                    ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>