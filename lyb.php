<?php
include 'pdo_connect.php';

$msg = '';
// 提交留言
if ($_POST) {
    $name = trim($_POST['name']);
    $content = trim($_POST['content']);

    if (empty($name) || empty($content)) {
        $msg = "<div>姓名和内容不能为空！</div>";
    } else {
        try {
            $sql = "INSERT INTO messages (name, content, create_time) VALUES (:name, :content, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':name' => $name, ':content' => $content]);
            $msg = "<div>留言发布成功！</div>";
        } catch (PDOException $e) {
            $msg = "<div>发布失败</div>";
        }
    }
}

// 查询留言
try {
    $stmt = $pdo->query("SELECT * FROM messages ORDER BY id DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages = [];
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<title>留言板</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: 'Microsoft YaHei', Arial, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
    }
    .container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    h1 {
        color: #333;
        text-align: center;
        margin-bottom: 25px;
        font-size: 28px;
        border-bottom: 3px solid #667eea;
        padding-bottom: 15px;
    }
    .msg-box {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
    }
    .msg-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .msg-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    form {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: bold;
        font-size: 16px;
    }
    input[type="text"] {
        width: 100%;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s;
        margin-bottom: 20px;
    }
    input[type="text"]:focus {
        border-color: #667eea;
        outline: none;
    }
    textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        resize: vertical;
        min-height: 120px;
        transition: border-color 0.3s;
        margin-bottom: 20px;
    }
    textarea:focus {
        border-color: #667eea;
        outline: none;
    }
    button[type="submit"] {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 40px;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        font-weight: bold;
    }
    button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    .message-list {
        margin-top: 20px;
    }
    .message-item {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .message-item:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .message-name {
        color: #667eea;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 10px;
    }
    .message-content {
        color: #333;
        line-height: 1.6;
        margin-bottom: 10px;
        padding: 10px;
        background: white;
        border-radius: 5px;
    }
    .message-time {
        color: #888;
        font-size: 14px;
    }
    .no-message {
        text-align: center;
        color: #888;
        padding: 40px;
        font-size: 18px;
    }
</style>
</head>
<body>
<div class="container">

<h1>发表留言</h1>
<?php 
    if(!empty($msg)) {
        $msgClass = strpos($msg, '成功') !== false ? 'msg-success' : 'msg-error';
        echo '<div class="msg-box ' . $msgClass . '">' . strip_tags($msg) . '</div>';
    }
?>

<form method="post">
    <label for="name">姓名：</label>
    <input type="text" id="name" name="name" placeholder="请输入您的姓名" required>
    
    <label for="content">留言内容：</label>
    <textarea id="content" name="content" placeholder="请输入留言内容..." required></textarea>
    
    <button type="submit">发布留言</button>
</form>

<hr>

<h1>留言列表</h1>
<div class="message-list">
<?php if(empty($messages)): ?>
    <div class="no-message">暂无留言，快来发表第一条留言吧！</div>
<?php else: ?>
    <?php foreach($messages as $m): ?>
    <div class="message-item">
        <div class="message-name"><?php echo htmlspecialchars(isset($m['name']) ? $m['name'] : (isset($m['username']) ? $m['username'] : '匿名用户')); ?></div>
        <div class="message-content"><?php echo nl2br(htmlspecialchars(isset($m['content']) ? $m['content'] : '')); ?></div>
        <div class="message-time"><?php echo isset($m['create_time']) ? $m['create_time'] : ''; ?></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

</div>
</body>
</html>