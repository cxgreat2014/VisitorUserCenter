<?php
require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';
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

$blogtitle = 'oauth2 - 防盗设置';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

function ReNewSiteCtrlJSFile($zbp) {
//*******************************************************BG*************************************************************************
    $SiteCtrljs = fopen("./js/SiteCtrl.js", "w") or die("Unable to open file!");

    if ($zbp->Config('oauth2')->normenu)
        fwrite($SiteCtrljs, "document.oncontextmenu = function() {
    event.returnValue = false;
}
document.oncontextmenu=function(e){
    return false;
}");
    if ($zbp->Config('oauth2')->noselect)
        fwrite($SiteCtrljs, '
document.onselectstart=function(){
	return false;
}
$("body").attr({style:"-webkit-touch-callout:none;-webkit-user-select:none;-moz-user-select:none;user-select:none;"});');

//-------------按键拦截------------
    if ($zbp->Config('oauth2')->nof5 || $zbp->Config('oauth2')->nof12 || $zbp->Config('oauth2')->nocs) {
        $js = "
document.onkeydown = function(e){
    if (";
        if ($zbp->Config('oauth2')->nof5)
            $js .= "e.keyCode == 116 ||(e.ctrlKey && e.keyCode==82)";
        if ($zbp->Config('oauth2')->nof12) {
            if ($zbp->Config('oauth2')->nof5)
                $js .= "||";
            $js .= "e.keyCode == 123 || (event.shiftKey && event.ctrlKey && (event.keyCode==73))";
        }
        if ($zbp->Config('oauth2')->nocs) {
            if ($zbp->Config('oauth2')->nof5 || $zbp->Config('oauth2')->nof12)
                $js .= "||";
            $js .= "event.ctrlKey && (event.keyCode==83)";
        }
        $js .= "){
		e.preventDefault();  
	}
}";
        fwrite($SiteCtrljs, $js);
    }

    if ($zbp->Config('oauth2')->noiframe)
        fwrite($SiteCtrljs, "
(function(window) {
	if (window.location !== window.top.location) {
		window.top.location = window.location;
	}
})(this);");
    if ($zbp->Config('oauth2')->closesite)
        fwrite($SiteCtrljs, '
$("body").html("<div style=\"position:fixed;top:0;left:0;width:100%;height:100%;text-align:center;background:#fff;padding-top:150px;z-index:99999;\">' . $zbp->Config('oauth2')->closetips . '</div>");');

    fclose($SiteCtrljs);
}

//***************************************ReNewSiteCtrlJSFile Stop*******************************************************************
?>
    <div id="divMain">
        <div class="divHeader"><?php echo $blogtitle; ?></div>
        <div class="SubMenu">
            <?php require dirname(__FILE__) . '/header.php'; ?>
            <!--<a href="main.php" ><span class="m-left">用户授权</span></a>
            <a href="SiteCtrl.php"><span class="m-left">防盗设置</span></a>
            <a href="history.php"><span class="m-left">记录查询</span></a>
            <a href=""><span class="m-left" style="color:#F60">有收费版？</span></a>
            <a href="http://app.zblogcn.com/?auth=115" target="_blank"><span class="m-left">作者作品</span></a>
            <a href="http://dev.junziruogong.xyz/" target="_blank"><span class="m-right">作者网站</span></a>-->
        </div>
        <div id="divMain2">
            <!--代码-->
            <?php
            if (isset($_POST['normenu'])) {
                $zbp->Config('oauth2')->siteprocted = $_POST['siteprocted'];
                $zbp->Config('oauth2')->normenu = $_POST['normenu'];
                $zbp->Config('oauth2')->noselect = $_POST['noselect'];
                $zbp->Config('oauth2')->nof5 = $_POST['nof5'];
                $zbp->Config('oauth2')->nof12 = $_POST['nof12'];
                $zbp->Config('oauth2')->nocs = $_POST['nocs'];
                $zbp->Config('oauth2')->noiframe = $_POST['noiframe'];
                $zbp->Config('oauth2')->closesite = $_POST['closesite'];
                $zbp->Config('oauth2')->closetips = $_POST['closetips'];
                $zbp->SaveConfig('oauth2');
                ReNewSiteCtrlJSFile($zbp);
                $zbp->SetHint('good');
                //Redirect('./SiteCtrl.php');
            }
            ?>
            <form action="SiteCtrl.php" method="post">
                <table class="tb-set" width="100%">
                    <tr>
                        <td colspan="2"><p style="color:#960;padding:0.8em;">
                                此处设定将影响前端JS代码，实现禁止右键、选择(复制)、屏蔽F5刷新、屏蔽开发者控制台入口，以及禁止网站被框架引用、临时关站等功能。可在<a href="main.php">用户管理</a>面板对特定用户放宽权限控制
                            </p></td>
                    </tr>
                    <tr>
                        <td align="right" width="15%" height="30"><b>站点保护：</b></td>
                        <td><span class="sel"><input name="siteprocted" id="siteprocted" class="checkbox" type="text"
                                                     value="<?php echo $zbp->Config('oauth2')->siteprocted; ?>"/></span><span
                                style="color:#888;margin-left:20px;font-size:12px;">禁止在非本站验证页面访问用户跟踪文件</span></td>
                    </tr>
                    <tr>
                        <td align="right" height="30"><b>禁用右键：</b></td>
                        <td><span class="sel"><input name="normenu" id="normenu" class="checkbox" type="text"
                                                     value="<?php echo $zbp->Config('oauth2')->normenu; ?>"/></span><span
                                style="color:#888;margin-left:20px;font-size:12px;">禁用鼠标右键菜单复制</span></td>
                    </tr>
                    <tr>
                        <td align="right" height="30"><b>禁止选择：</b></td>
                        <td><span class="sel"><input name="noselect" id="noselect" class="checkbox" type="text"
                                                     value="<?php echo $zbp->Config('oauth2')->noselect; ?>"/></span><span
                                style="color:#888;margin-left:20px;font-size:12px;">禁止选择网页内容可起到防Ctrl+C复制网页内容的作用</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" height="30"><b>禁用F5：</b></td>
                        <td><span class="sel"><input name="nof5" id="nof5" class="checkbox" type="text"
                                                     value="<?php echo $zbp->Config('oauth2')->nof5; ?>"/></span><span
                                style="color:#888;margin-left:20px;font-size:12px;">防止使用F5键频繁刷新网页造成恶意流量攻击</span></td>
                    </tr>
                    <tr>
                        <td align="right" height="30"><b>屏蔽开发者控制台入口：</b></td>
                        <td><span class="sel"><input name="nof12" id="nof12" class="checkbox" type="text"
                                                     value="<?php echo $zbp->Config('oauth2')->nof12; ?>"/></span><span
                                style="color:#888;margin-left:20px;font-size:12px;">禁用F12键能防止被分析网页元素或通过浏览器控制台复制网页内容</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" height="30"><b>禁用Ctrl+S保存网页：</b></td>
                        <td><span class="sel"><input name="nocs" id="nocs" class="checkbox" type="text"
                                                     value="<?php echo $zbp->Config('oauth2')->nocs; ?>"/></span><span
                                style="color:#888;margin-left:20px;font-size:12px;">防止使用Ctrl+S保存网页</span></td>
                    </tr>
                    <tr>
                        <td align="right" height="30"><b>禁止被框架引用：</b></td>
                        <td><span class="sel"><input name="noiframe" id="noiframe" class="checkbox" type="text"
                                                     value="<?php echo $zbp->Config('oauth2')->noiframe; ?>"/></span><span
                                style="color:#888;margin-left:20px;font-size:12px;">当本站被框架引用时自动跳出，防止恶意盗链或反射攻击<a
                                    href="http://i.ewceo.com/respond.html" target="_blank"
                                    style="color:#39C;margin-left:20px;" title="响应式网页测试工具模拟框架引用">在线测试</a></span></td>
                    </tr>
                    <tr>
                        <td align="right" height="30"><b>关闭网站：</b></td>
                        <td><span class="sel"><input name="closesite" id="closesite" class="checkbox" type="text"
                                                     value="<?php echo $zbp->Config('oauth2')->closesite; ?>"/></span><span
                                style="color:#888;margin-left:20px;font-size:12px;">JS方式临时关闭网站，不影响网站内容和搜索引擎收录</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><b>关闭网站公告：</b></td>
                        <td><span class="sel"><textarea name="closetips" cols="40" rows="4"
                                                        style="vertical-align:middle"><?php echo $zbp->Config('oauth2')->closetips; ?></textarea></span>
                        </td>
                    </tr>
                    <tr>
                        <td height="50">&nbsp;</td>
                        <td><input type="submit" name="submit" value="保存设置"/></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <script
        type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/oauth2/logo.png';?>");</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>