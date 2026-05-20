<?php
session_start();
$user_name = isset($_SESSION["user_name"]) ? $_SESSION["user_name"] : "";

// 未登录判断
if($user_name == ""){
    echo '<meta http-equiv="refresh" content="3;url=login.php">';
    echo '<div style="text-align:center;margin-top:100px;font-size:18px;">';
    echo '请登录后再次访问，<span id="miao">3</span>秒后返回登录页面<br><br>';
    echo '<a href="login.php" style="color:#0066cc;">点击直接返回</a>';
    echo '</div>';
    header("refresh:3;url=login.php");
    exit();
}