<?php
define('UC_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "\\1", str_replace('\\', '/', __FILE__)));
define('site_path', preg_replace("/(\/zb_users\/plugin\/VisitorUserCenter\/).*/is", "", str_replace('\\', '/', __FILE__)) . "/");
require_once UC_path . 'ControlPanel/main.php';
$blogtitle = 'VisitorUserCenter - 防盗设置';
global $zbp;
global $vuc;
require dirname(__FILE__) . '/header.php';
?>
    <div class="divHeader"><?php echo $blogtitle; ?></div>
    <div id="divMain2">
        <?php
        if (isset($_POST['normenu'])) {
            $vuc->SetConfig('siteprocted', $_POST['siteprocted']);
            $vuc->SetConfig('normenu', $_POST['normenu']);
            $vuc->SetConfig('noselect', $_POST['noselect']);
            $vuc->SetConfig('nof5', $_POST['nof5']);
            $vuc->SetConfig('nof12', $_POST['nof12']);
            $vuc->SetConfig('nocs', $_POST['nocs']);
            $vuc->SetConfig('noiframe', $_POST['noiframe']);
            $vuc->SetConfig('closesite', $_POST['closesite']);
            $vuc->SetConfig('closetips', $_POST['closetips']);
            ReNewSiteCtrlJSFile();
            $zbp->SetHint('good');
            Redirect('./SiteCtrl.php');
        }

        ?>
        <form action="SiteCtrl.php" method="post">
            <table class="tb-set" width="100%">
                <tr>
                    <td colspan="2"><p style="color:#960;padding:0.8em;">
                            此处设定将影响前端JS代码，实现禁止右键、选择(复制)、屏蔽F5刷新、屏蔽开发者控制台入口，以及禁止网站被框架引用、临时关站等功能。可在<a
                                href="main.php">用户管理</a>面板对特定用户放宽权限控制
                        </p></td>
                </tr>
                <tr>
                    <td align="right" width="15%" height="30"><b>站点保护：</b></td>
                    <td><span class="sel"><input name="siteprocted" id="siteprocted" class="checkbox" type="text"
                                                 value="<?php echo $vuc->GetConfig('siteprocted'); ?>"/></span><span
                            style="color:#888;margin-left:20px;font-size:12px;">禁止在非本站验证页面访问用户跟踪文件</span></td>
                </tr>
                <tr>
                    <td align="right" height="30"><b>禁用右键：</b></td>
                    <td><span class="sel"><input name="normenu" id="normenu" class="checkbox" type="text"
                                                 value="<?php echo $vuc->GetConfig('normenu'); ?>"/></span><span
                            style="color:#888;margin-left:20px;font-size:12px;">禁用网页鼠标右键菜单</span></td>
                </tr>
                <tr>
                    <td align="right" height="30"><b>禁止选择：</b></td>
                    <td><span class="sel"><input name="noselect" id="noselect" class="checkbox" type="text"
                                                 value="<?php echo $vuc->GetConfig('noselect'); ?>"/></span><span
                            style="color:#888;margin-left:20px;font-size:12px;">禁止选择网页内容可起到防Ctrl+C复制网页内容的作用</span>
                    </td>
                </tr>
                <tr>
                    <td align="right" height="30"><b>禁用F5：</b></td>
                    <td><span class="sel"><input name="nof5" id="nof5" class="checkbox" type="text"
                                                 value="<?php echo $vuc->GetConfig('nof5'); ?>"/></span><span
                            style="color:#888;margin-left:20px;font-size:12px;">防止使用F5键频繁刷新网页造成恶意流量攻击</span></td>
                </tr>
                <tr>
                    <td align="right" height="30"><b>屏蔽开发者控制台入口：</b></td>
                    <td><span class="sel"><input name="nof12" id="nof12" class="checkbox" type="text"
                                                 value="<?php echo $vuc->GetConfig('nof12'); ?>"/></span><span
                            style="color:#888;margin-left:20px;font-size:12px;">禁用F12键能防止被分析网页元素或通过浏览器控制台复制网页内容</span>
                    </td>
                </tr>
                <tr>
                    <td align="right" height="30"><b>禁用Ctrl+S保存网页：</b></td>
                    <td><span class="sel"><input name="nocs" id="nocs" class="checkbox" type="text"
                                                 value="<?php echo $vuc->GetConfig('nocs'); ?>"/></span><span
                            style="color:#888;margin-left:20px;font-size:12px;">防止使用Ctrl+S保存网页</span></td>
                </tr>
                <tr>
                    <td align="right" height="30"><b>禁止被框架引用：</b></td>
                    <td><span class="sel"><input name="noiframe" id="noiframe" class="checkbox" type="text"
                                                 value="<?php echo $vuc->GetConfig('noiframe'); ?>"/></span><span
                            style="color:#888;margin-left:20px;font-size:12px;">当本站被框架引用时自动跳出，防止恶意盗链或反射攻击<a
                                href="http://i.ewceo.com/respond.html" target="_blank"
                                style="color:#39C;margin-left:20px;" title="响应式网页测试工具模拟框架引用">在线测试</a></span></td>
                </tr>
                <tr>
                    <td align="right" height="30"><b>关闭网站：</b></td>
                    <td><span class="sel"><input name="closesite" id="closesite" class="checkbox" type="text"
                                                 value="<?php echo $vuc->GetConfig('closesite'); ?>"/></span><span
                            style="color:#888;margin-left:20px;font-size:12px;">JS方式临时关闭网站，不影响网站内容和搜索引擎收录</span>
                    </td>
                </tr>
                <tr>
                    <td align="right"><b>关闭网站公告：</b></td>
                    <td><span class="sel"><textarea name="closetips" cols="40" rows="4"
                                                    style="vertical-align:middle"><?php echo $vuc->GetConfig('closetips'); ?></textarea></span>
                    </td>
                </tr>
                <tr>
                    <td height="50">&nbsp;</td>
                    <td><input type="submit" name="submit" value="保存设置"/></td>
                </tr>
            </table>
        </form>
    </div>
<?php require UC_path . 'ControlPanel/footer.php'; ?>