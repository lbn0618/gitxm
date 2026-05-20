<?php
include 'pdo_connect.php';
//$sql = "CREATE TABLE IF NOT EXISTS lyb(
//id INT AUTO_INCREMENT PRIMARY KEY,
//name VARCHAR(30) NOT NULL,
//content TEXT NOT NULL,
//create_time DATETIME DEFAULT CURRENT_TIMESTAMP
//)ENGINE= InnoDB DEFAULT CHARSET=utf8mb4;";
//$result = $pdo->exec($sql);
//if ($result !== false) {
//    echo "创建成功";
//} else {
//    $error = $pdo->errorInfo();
//    echo "创建失败" . $error[2];
//}
// 提交留言处理

if (!empty($_POST['name']) && !empty($_POST['content'])) {
    $name = trim($_POST['name']);
    $content = trim($_POST['content']);
    $sql = "INSERT INTO lyb (name, content) VALUES (:name, :content)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $content]);
    header('Location: '.$_SERVER['PHP_SELF']);     //防止重复提交
    exit;

}
if (isset($_GET['del_id']) && is_numeric($_GET['del_id'])) {
    $del_id = (int)$_GET['del_id'];     //强制把传过来的 ID 转成整数
    $delSql = "DELETE FROM lyb WHERE id = :id LIMIT 1";     //只删除对应ID的那一条
    $delStmt = $pdo->prepare($delSql);
    $delStmt->execute([':id'=>$del_id]);
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;

}
// 查询留言
$sql = "SELECT * FROM lyb ORDER BY create_time DESC"; //按时间倒叙查询留言
$stmt = $pdo->query($sql);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);     //获取所有留言数据
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>留言板</title>
</head>
<body>
    <h2>留言板</h2>
    <form method="post">
        昵称：<input type="text" name="name" required><br><br>
        留言内容：<textarea name="content" rows="4" cols="50" required></textarea><br><br>
        <button type="submit">提交留言</button>
    </form>
    <hr>
    <h3>全部留言</h3>
    <?php foreach ($list as $v): ?>
    <div>
        昵称：<?php echo htmlspecialchars($v['name']); ?>
        时间：<?php echo $v['create_time']; ?>
        <a href="liuyanban.php?del_id=<?php echo $v['id']; ?>" class="del-btn" onclick="return)">删除</a>
        <p>内容：<?php echo htmlspecialchars($v['content']); ?></p>
        <hr>

    </div>
    <?php endforeach; ?>


</body>
</html>



