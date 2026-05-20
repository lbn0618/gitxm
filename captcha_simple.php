<?php
session_start();

// 开启错误显示
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 检查GD库
if (!extension_loaded('gd')) {
    die('GD库未启用，请在php.ini中启用 extension=gd');
}

// 生成随机验证码
function generateCode($length = 4) {
    $chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $code;
}

// 生成验证码
$code = generateCode(4);
$_SESSION['captcha'] = strtolower($code);

// 图片尺寸
$width = 120;
$height = 40;

// 创建图片
$image = imagecreatetruecolor($width, $height);
if (!$image) {
    die('创建图片失败');
}

// 背景色 - 白色
$bgColor = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $bgColor);

// 添加干扰线
for ($i = 0; $i < 5; $i++) {
    $lineColor = imagecolorallocate($image, mt_rand(100, 200), mt_rand(100, 200), mt_rand(100, 200));
    imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
}

// 添加干扰点
for ($i = 0; $i < 50; $i++) {
    $pointColor = imagecolorallocate($image, mt_rand(100, 200), mt_rand(100, 200), mt_rand(100, 200));
    imagesetpixel($image, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
}

// 绘制验证码文字 - 使用更大的字体
$fontSize = 5; // 内置字体大小 1-5
$x = 10;
for ($i = 0; $i < strlen($code); $i++) {
    $charColor = imagecolorallocate($image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
    $y = mt_rand(8, 15);
    imagestring($image, $fontSize, $x + $i * 25, $y, $code[$i], $charColor);
}

// 输出图片
header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

imagepng($image);
imagedestroy($image);
?>
