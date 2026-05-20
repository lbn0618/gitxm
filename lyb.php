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
</head>
<body>

<h1>发表留言</h1>
<?php echo $msg; ?>

<form method="post">
姓名：<input type="text" name="name" required><br><br>
内容：<br>
<textarea name="content" required></textarea><br><br>
<button type="submit">发布留言</button>
</form>

<hr>

<h1>留言列表</h1>
<?php if(empty($messages)): ?>
暂无留言
<?php else: ?>
    <?php foreach($messages as $m): ?>
<div>
姓名：<?php echo htmlspecialchars($m['name']); ?><br>
内容：<?php echo htmlspecialchars($m['content']); ?><br>
时间：<?php echo $m['create_time']; ?>
</div>
<hr>
<?php endforeach; ?>
<?php endif; ?>

</body>
</html>