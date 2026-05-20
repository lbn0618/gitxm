<?php
// 测试GD库是否可用
echo '<h2>PHP GD库检测</h2>';

if (extension_loaded('gd')) {
    echo '<p style="color:green">✓ GD库已启用</p>';
    echo '<p>GD版本: ' . GD_VERSION . '</p>';
    echo '<p>支持的图片格式:</p>';
    echo '<ul>';
    if (function_exists('imagepng')) echo '<li>PNG - 支持</li>';
    if (function_exists('imagejpeg')) echo '<li>JPEG - 支持</li>';
    if (function_exists('imagegif')) echo '<li>GIF - 支持</li>';
    echo '</ul>';
    
    // 测试生成简单图片
    $im = imagecreatetruecolor(100, 30);
    $bg = imagecolorallocate($im, 255, 255, 255);
    $text_color = imagecolorallocate($im, 0, 0, 0);
    imagestring($im, 5, 10, 8, 'Test OK', $text_color);
    
    echo '<p>测试图片:</p>';
    echo '<img src="data:image/png;base64,' . base64_encode(imagepng($im)) . '">';
    
    imagedestroy($im);
} else {
    echo '<p style="color:red">✗ GD库未启用</p>';
    echo '<p>请在php.ini中启用GD库:</p>';
    echo '<pre>extension=gd</pre>';
    echo '<p>或者联系服务器管理员启用GD库</p>';
}

// 显示PHP信息
echo '<hr><p><a href="?phpinfo=1">查看完整PHP信息</a></p>';
if (isset($_GET['phpinfo'])) {
    phpinfo();
}
?>
