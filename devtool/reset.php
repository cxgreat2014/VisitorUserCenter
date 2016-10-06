<?php
require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}/*
if (!$zbp->CheckPlugin('oauth2')) {
    $zbp->ShowError(48);
    die();
}*/

if ($_GET['reset'] == "true") {
    oauth2_Reset();
}
function oauth2_Reset() {
    global $zbp;
    $user_table = $GLOBALS['table']['plugin_oauth2_user'];
    $group_table = $GLOBALS['table']['plugin_oauth2_group'];
    $history_table = $GLOBALS['table']['plugin_oauth2_history'];
    $sql = "";
    if ($zbp->db->ExistTable($user_table)) {
        $sql .= $zbp->db->sql->DelTable($user_table) . ";";
    }
    if ($zbp->db->ExistTable($group_table)) {
        $sql .= $zbp->db->sql->DelTable($group_table) . ";";
    }
    if ($zbp->db->ExistTable($history_table)) {
        $sql .= $zbp->db->sql->DelTable($history_table) . ";";
    }
    $vuc=new VUC();
    echo '<p>Reset Finished</p><p>Plugin version:'.$vuc->GetConfig('version').'</p>';//<script>window.setTimeout(\"window.location='../main.php'\",2000); </script>";
    die();
} ?>

<html>
<head>
    <style>
        #parent {
            display: table;
        }

        #child {
            display: table-cell;
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div>
    <div>
        <div id="parent">
            <div id="child">
                <a href="reset.php?reset=true" style="top:50%;">
                    <button>Click Me to Reset Database</button>
                </a>

            </div>
        </div>
    </div>
</div>
</body>
</html>
