<?php
session_start();
include 'pdo_connect.php';
$pdo->exec("SET NAMES utf8mb4");

if(isset($_GET['logout'])){
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
    header("Location: denglu.php");
    exit();
}

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: denglu.php");
    exit();
}

if(isset($_GET['del'])){
    $id = (int)$_GET['del'];
    if($id === $_SESSION['uid']){
        die("不能删除自己！");
    }
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='user'");
        $stmt->execute([$id]);
        $stmt_msg = $pdo->prepare("DELETE FROM messages WHERE uid=?");
        $stmt_msg->execute([$id]);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("删除失败：" . $e->getMessage());
    }
    header("Location: admin.php");
    exit();
}

if(isset($_GET['change_role'])){
    $id = (int)$_GET['change_role'];
    $new_role = $_GET['new_role'] === 'admin' ? 'admin' : 'user';
    if($id === $_SESSION['uid']){
        die("不能修改自己的权限！");
    }
    if($new_role === 'user'){
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role='admin'");
        $admin_count = $stmt->fetchColumn();
        if($admin_count <= 1){
            die("至少需要保留一个管理员！");
        }
    }
    try {
        $stmt = $pdo->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->execute([$new_role, $id]);
        header("Location: admin.php");
        exit();
    } catch (Exception $e) {
        die("权限修改失败：" . $e->getMessage());
    }
}
$stmt = $pdo->query("SELECT id,username,role FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>管理员后台</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Microsoft YaHei", sans-serif;
        }

        body {
            background-color: #f5f7fa;
            padding: 30px;
            max-width: 900px;
            margin: 0 auto;
            line-height: 1.6;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
            padding-left: 12px;
        }

        h3 {
            color: #34495e;
            margin: 25px 0 15px;
            font-size: 18px;
        }

        p {
            color: #555;
            margin: 8px 0;
        }

        .user-item {
            background: white;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .user-item strong {
            color: #222;
        }

        .role-admin {
            color: #e74c3c;
            font-weight: bold;
        }

        .role-user {
            color: #2ecc71;
            font-weight: bold;
        }

        a {
            color: #3498db;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
        }

        a:hover {
            background-color: #3498db;
            color: white;
        }

        .delete-btn {
            color: #e74c3c;
        }

        .delete-btn:hover {
            background-color: #e74c3c;
            color: white;
        }

        .welcome {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .logout {
            display: inline-block;
            margin-top: 20px;
            background: #95a5a6;
            color: white;
            padding: 6px 12px;
        }

        .logout:hover {
            background: #7f8c8d;
        }

        .empty {
            color: #999;
            padding: 20px;
            text-align: center;
            background: white;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<h2>管理员后台</h2>
<p class="welcome">欢迎你：<?php echo htmlspecialchars($_SESSION['user']); ?></p>
<p><a href="user.php">进入留言板</a></p>

<h3>用户列表</h3>
<?php if(empty($users)): ?>
    <div class="empty">暂无用户</div>
<?php else: ?>
    <?php foreach($users as $u): ?>
        <div class="user-item">
            <span>ID：<?php echo $u['id']; ?></span>
            <span>用户名：<?php echo htmlspecialchars($u['username']); ?></span>
            <span>
                    权限：
                    <?php if($u['role'] === 'admin'): ?>
                        <span class="role-admin">【管理员】</span>
                    <?php else: ?>
                        <span class="role-user">【普通用户】</span>
                    <?php endif; ?>
                </span>
            <?php if($u['id'] != $_SESSION['uid']): ?>
                <?php if($u['role'] === 'user'): ?>
                    <a href="?change_role=<?php echo $u['id']; ?>&new_role=admin" onclick="return confirm('确定将该用户设为管理员吗？')">设为管理员</a>
                <?php else: ?>
                    <a href="?change_role=<?php echo $u['id']; ?>&new_role=user" onclick="return confirm('确定将该管理员改为普通用户吗？')">取消管理员</a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if($u['role'] === 'user'): ?>
                <a href="?del=<?php echo $u['id']; ?>" class="delete-btn" onclick="return confirm('确定删除该用户？该用户所有留言也会被删除！')">删除</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<p><a href="?logout=1" class="logout">退出登录</a></p>
</body>
</html>
