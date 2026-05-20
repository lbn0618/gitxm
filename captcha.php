<?php
session_start();

// 生成随机验证码
function generateCode($length = 4) {
    $chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $code;
}

// 生成验证码图片
$code = generateCode(4);
$_SESSION['captcha'] = strtolower($code);

// 图片尺寸
$width = 120;
$height = 40;

// 创建图片
$image = imagecreatetruecolor($width, $height);

// 背景色
$bgColor = imagecolorallocate($image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
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

// 绘制验证码文字
$textColor = imagecolorallocate($image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
$fontSize = 5;
$x = 15;
for ($i = 0; $i < strlen($code); $i++) {
    $charColor = imagecolorallocate($image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
    imagestring($image, $fontSize, $x + $i * 25, mt_rand(8, 15), $code[$i], $charColor);
}

// 输出图片
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>
