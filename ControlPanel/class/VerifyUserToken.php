<?php
/**
 * 使用Get的方式返回：challenge和capthca_id 此方式以实现前后端完全分离的开发模式 专门实现failback
 * @author Tanxu
 */
//error_reporting(0);
require_once dirname(__FILE__) . '/class.geetestlib.php';
//Config.php
define("CAPTCHA_ID", "b46d1900d0a894591916ea94ea91bd2c");
define("PRIVATE_KEY", "36fc3fe98530eea08dfc6ce76e3d24c4");
//End

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
    session_start();
    $user_id = "test";
    $status = $GtSdk->pre_process($user_id);
    $_SESSION['gtserver'] = $status;
    $_SESSION['user_id'] = $user_id;
    echo $GtSdk->get_response_str();
} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
    session_start();
    $GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
    $user_id = $_SESSION['user_id'];
    if ($_SESSION['gtserver'] == 1) {
        if ($GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $user_id)) {
            UserLogin();
        } else {
            echo 'alert("error!");';
        }
    } else {
        if ($GtSdk->fail_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'])) {
            UserLogin();
        } else {
            echo 'alert("error!");';
        }
    }
}

function UserLogin() {
    if ($_POST["user"] == "admin") {
        echo 'alert("Yes,admin");';
    } else {
        echo 'alert("Not Admin,' . $_POST["user"] . '2");';
    }
}

?>