<?php
echo '
<div class="SubMenu">
    <a href="main.php"><span class="m-left">用户管理</span></a>
    <a href="Group.php"><span class="m-left">分组授权</span></a>
    <a href="SiteCtrl.php"><span class="m-left">防盗设置</span></a>
    <a href="History.php"><span class="m-left">记录查询</span></a>
    <a href="DarkNetmod.php"><span class="m-left">暗网模式</span></a>
</div>
<style>
    input[type=checkbox]:focus, select:focus, a:focus {
        outline: 0;
        border-color: rgba(82, 168, 236, 0.8);
        -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 0 8px rgba(82, 168, 236, 0.6); /* Safari 4 */
        -moz-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 0 8px rgba(82, 168, 236, 0.6); /* Firefox 3.6 */
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 0 8px rgba(82, 168, 236, 0.6);
    }

    tr:not(:first-child):hover {
        background: #ffffdd;
    }

    tr:last-child:hover {
        background: inherit;
    }

    label, span {
        -moz-user-select: none; /*火狐*/
        -webkit-user-select: none; /*webkit浏览器*/
        -ms-user-select: none; /*IE10*/
        -khtml-user-select: none; /*早期浏览器*/
        user-select: none;
    }

    tr td:nth-child(3) {
        padding-left: 10px;
    }

    p#vtip {
        display: none;
        position: absolute;
        padding: 10px;
        font-size: 0.8em;
        background-color: white;
        border: 1px solid #a6c9e2;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        z-index: 9999
    }

    p#vtip #vtipArrow {
        position: absolute;
        top: 39px;
        left: 5px;
        transform: rotate(180deg);
    }

    input[type="text"] {
        width: 100px;
    }

    select {
        width: 120px;
    }

    .lbt {
        padding: 8px 4px;
        cursor: pointer;
        border: none;
        background: #E1E1E1;
        margin: 3px 3px 3px 7px;
    }
</style>
';
