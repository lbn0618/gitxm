<?php
session_start();
if(!isset($_SESSION['uid'])){
    header("Location: denglu.php");
    exit();
}
include 'pdo_connect.php';
$update_pwd_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pwd'])) {
    $old_pwd = trim($_POST['old_pwd']);
    $new_pwd = trim($_POST['new_pwd']);
    $confirm_pwd = trim($_POST['confirm_pwd']);
    $uid = $_SESSION['uid'];
    if (empty($old_pwd) || empty($new_pwd) || empty($confirm_pwd)) {
        $update_pwd_msg = "<p style='color:red;'>所有密码框不能为空</p>";
    } elseif ($new_pwd !== $confirm_pwd) {
        $update_pwd_msg = "<p style='color:red;'>两次新密码不一致</p>";
    } else {
        // 验证原密码
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        $user = $stmt->fetch();
        if ($user && password_verify($old_pwd, $user['password'])) {
            $new_pwd_hash = password_hash($new_pwd, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$new_pwd_hash, $uid]);
            $update_pwd_msg = "<p style='color:green;'>密码修改成功！<a href='user.php'>返回用户中心</a></p>";
        } else {
            $update_pwd_msg = "<p style='color:red;'>原密码错误</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>修改密码</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Microsoft YaHei", sans-serif;
        }
        body {
            background-color: #f5f7fa;
            padding: 40px 20px;
        }
        .container {
            max-width: 450px;
            margin: 0 auto;
            background: #fff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-bottom: 20px;
            color: #409eff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .msg {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        form label {
            display: inline-block;
            width: 80px;
            font-size: 14px;
            color: #555;
        }
        form input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 5px;
        }
        form input:focus {
            outline: none;
            border-color: #409eff;
        }
        button {
            padding: 11px;
            background-color: #409eff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #337ecc;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>修改密码</h2>
    <a href="user.php" class="back-link">← 返回用户中心</a>

    <div class="msg"><?= $update_pwd_msg ?></div>

    <form method="post">
        <div>
            原密码：<input type="password" name="old_pwd" required>
        </div>
        <div>
            新密码：<input type="password" name="new_pwd" required>
        </div>
        <div>
            确认新密码：<input type="password" name="confirm_pwd" required>
        </div>
        <button type="submit" name="update_pwd">确认修改</button>
    </form>
</div>
</body>
</html>
