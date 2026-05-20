<?php
session_start();
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// 生成滑块验证码
if ($action === 'generate') {
    // 背景图宽度高度
    $width = 280;
    $height = 160;
    $blockSize = 50;
    
    // 创建背景图
    $image = imagecreatetruecolor($width, $height);
    
    // 渐变背景
    for ($y = 0; $y < $height; $y++) {
        $color = imagecolorallocate($image, 
            mt_rand(100, 200), 
            mt_rand(100, 200), 
            mt_rand(100, 200));
        imageline($image, 0, $y, $width, $y, $color);
    }
    
    // 添加随机图形装饰
    for ($i = 0; $i < 20; $i++) {
        $shapeColor = imagecolorallocatealpha($image, 
            mt_rand(255, 255), 
            mt_rand(255, 255), 
            mt_rand(255, 255), 
            mt_rand(50, 100));
        imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 
            mt_rand(10, 40), mt_rand(10, 40), $shapeColor);
    }
    
    // 滑块位置
    $blockX = mt_rand($blockSize + 20, $width - $blockSize - 20);
    $blockY = mt_rand(20, $height - $blockSize - 20);
    
    // 绘制滑块区域（挖空效果）
    $blockImage = imagecreatetruecolor($blockSize, $blockSize);
    $transparent = imagecolorallocatealpha($blockImage, 0, 0, 0, 127);
    imagefill($blockImage, 0, 0, $transparent);
    
    // 复制滑块区域
    for ($x = 0; $x < $blockSize; $x++) {
        for ($y = 0; $y < $blockSize; $y++) {
            $srcX = $blockX + $x;
            $srcY = $blockY + $y;
            if ($srcX < $width && $srcY < $height) {
                $color = imagecolorat($image, $srcX, $srcY);
                imagesetpixel($blockImage, $x, $y, $color);
            }
        }
    }
    
    // 在背景图上绘制滑块凹槽
    $slotColor = imagecolorallocatealpha($image, 0, 0, 0, 80);
    imagefilledrectangle($image, $blockX, $blockY, $blockX + $blockSize, $blockY + $blockSize, $slotColor);
    imagerectangle($image, $blockX, $blockY, $blockX + $blockSize, $blockY + $blockSize, 
        imagecolorallocate($image, 255, 255, 255));
    
    // 保存验证位置到session
    $_SESSION['slider_x'] = $blockX;
    $_SESSION['slider_time'] = time();
    
    // 输出图片为base64
    ob_start();
    imagepng($image);
    $bgBase64 = base64_encode(ob_get_clean());
    
    ob_start();
    imagepng($blockImage);
    $blockBase64 = base64_encode(ob_get_clean());
    
    imagedestroy($image);
    imagedestroy($blockImage);
    
    echo json_encode([
        'code' => 0,
        'bg_image' => 'data:image/png;base64,' . $bgBase64,
        'block_image' => 'data:image/png;base64,' . $blockBase64,
        'block_y' => $blockY
    ]);
    exit;
}

// 验证滑块
if ($action === 'verify') {
    $x = intval($_POST['x'] ?? 0);
    $expectedX = $_SESSION['slider_x'] ?? -1;
    $time = $_SESSION['slider_time'] ?? 0;
    
    // 验证有效期（5分钟）
    if (time() - $time > 300) {
        echo json_encode(['code' => 1, 'msg' => '验证码已过期']);
        exit;
    }
    
    // 允许误差范围 ±5像素
    if (abs($x - $expectedX) <= 5) {
        $_SESSION['slider_verified'] = true;
        echo json_encode(['code' => 0, 'msg' => '验证成功']);
    } else {
        echo json_encode(['code' => 1, 'msg' => '验证失败，请重试']);
    }
    exit;
}

echo json_encode(['code' => 1, 'msg' => '无效操作']);
?>