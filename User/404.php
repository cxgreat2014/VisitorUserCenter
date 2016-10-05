<?php
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="https://dn-daoerror-page.qbox.me/bower_components/normalize-css/normalize.css">
    <link rel="stylesheet" href="https://dn-daoerror-page.qbox.me/styles/css/main.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://dn-daoerror-page.qbox.me/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="https://dn-daoerror-page.qbox.me/bower_components/parallax/deploy/jquery.parallax.js"></script>
</head>
<body>
<div id="container" class="error_404">
    <ul id="scene">
        <li class="layer" data-depth="0.10"><div class="star diamond"></div></li>
        <li class="layer" data-depth="0.30"><div class="star dot"></div></li>
        <li class="layer" data-depth="0.50"><div class="star sparkle"></div></li>
        <li class="layer" data-depth="0.05"><div class="lighthouse"></div></li>
        <li class="layer" data-depth="0.20"><div class="wave dark-blue depth-20"></div></li>
        <li class="layer" data-depth="0.40"><div class="wave medium-blue depth-40"></div></li>
        <li class="layer" data-depth="0.60"><div class="wave light-blue depth-60"></div></li>
        <li class="layer" data-depth="0.00">
            <div class="error-message">
                <p style="font-size: 26px;">';
$privkeypass = '111111'; //私钥密码
$pfxpath = "../System/test.pfx"; //密钥文件路径
$priv_key = file_get_contents($pfxpath); //获取密钥文件内容
//$data = "test"; //加密数据测试test
/*
//私钥加密
openssl_pkcs12_read($priv_key, $certs, $privkeypass); //读取公钥、私钥
$prikeyid = $certs['pkey']; //私钥
openssl_sign($_POST['c'], $signMsg, $prikeyid,OPENSSL_ALGO_SHA512); //注册生成加密信息
$signMsg = base64_encode($signMsg); //base64转码加密信息
*/
$unsignMsg=base64_decode($signMsg);//base64解码加密信息
openssl_pkcs12_read($priv_key, $certs, $privkeypass); //读取公钥、私钥
$pubkeyid = $certs['cert']; //公钥
$res = openssl_verify($data, $unsignMsg, $pubkeyid); //验证

if(empty($_COOKIE['AD_Token'])||$_COOKIE['AD_Token']!=sha1()) {
    $strlist = str_split("秋风吹散了落叶,随之而去的还有您的文件", 3);
}elseif ($_COOKIE['AD_Token'])
foreach ($strlist as $item) {
    echo "<span>" . $item . "</span>";
}
echo '</p>
                <div class="button" style="width: 66px;"><a href="https://www.daocloud.io">回到首页</a></div>
            </div>
        </li>
    </ul>
</div>
</body>
<script type="text/javascript">
    $("#scene").parallax({
        invertX: false,
        invertY: false,
        limitX: false,
        limitY: false,
        scalarX: 10,
        scalarY: 8,
        frictionX: 0.5,
        frictionY: 0.5
    });
    $("span").click(function () {
        console.log($.trim($(this).text()))
    });
</script>
</html>
';
