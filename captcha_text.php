<?php
session_start();

// 生成随机验证码
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$code = '';
for ($i = 0; $i < 4; $i++) {
    $code .= $chars[random_int(0, strlen($chars) - 1)];
}

// 存入session
$_SESSION['captcha'] = strtolower($code);
$_SESSION['captcha_time'] = time();

// 返回JSON格式
header('Content-Type: application/json');
echo json_encode([
    'code' => $code,
    'success' => true
]);
