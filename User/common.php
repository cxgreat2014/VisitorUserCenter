<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/19
 * Time: 23:28
 */

function Redict($url) {
    header("location:" . $url);
}

function Iframe($url) {
    echo '<html><iframe src="' . $url . '" style="position:absolute; left:0; top:0; height:100%; width:100%; border:0;" scrolling="no"></iframe></html>';
}

function GetEncodeImgCss($fp) {
    return 'background-image: url("data:image/jpg;base64,' . base64_encode(file_get_contents($fp)) . '");';
}