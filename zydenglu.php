<?php
session_start();
include 'pdo_connect.php';
$msg = '';
$remembered_username = '';
if (isset($_COOKIE['remembered_username'])) {
    $remembered_username = $_COOKIE['remembered_username'];
}
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    session_destroy();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    header("Location: denglu.php");
    exit();
}
if ($_POST) {
    $username = trim($_POST['username']);
    $pwd = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? 1 : 0;
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();
    if($user && password_verify($pwd, $user['password'])){
        if ($remember) {
            setcookie('remembered_username', $username, time() + 7 * 24 * 60 * 60, '/');
        } else {
            setcookie('remembered_username', '', time() - 3600, '/');
        }
        // 登录成功，保存SESSION
        $_SESSION['uid'] = $user['id'];
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // 根据身份跳转到不同页面
        if($user['role'] === 'admin'){
            header("Location: admin.php");
            exit();
        } else {
            header("Location: user.php");
            exit();
        }
    }else{
        $msg = "<div class='error-msg'>账号或密码错误</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>用户登录</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Microsoft YaHei", sans-serif;
        }

        body {
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-box {
            background: #fff;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
        }

        .error-msg {
            color: #e53935;
            background: #ffebee;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        form p {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            height: 44px;
            padding: 0 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            transition: border 0.3s;
        }

        input:focus {
            border-color: #409eff;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #666;
        }

        button[type="submit"] {
            width: 100%;
            height: 46px;
            background: #409eff;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button[type="submit"]:hover {
            background: #338eef;
        }

        .btn-group {
            margin-top: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-group button {
            height: 40px;
            background: transparent;
            color: #409eff;
            border: 1px solid #409eff;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-group button:hover {
            background: #f0f7ff;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>用户登录</h2>
    <?php echo $msg; ?>
    <form method="post">
        <p>用户名：<input type="text" name="username" value="<?php echo htmlspecialchars($remembered_username); ?>"></p>
        <p>密　码：<input type="password" name="password"></p>
        <p class="remember">
            <input type="checkbox" name="remember" id="remember" <?php echo $remembered_username ? 'checked' : ''; ?>>
            <label for="remember">记住用户名</label>
        </p>
        <button type="submit">登录</button>
    </form>

    <div class="btn-group">
        <button type="button" onclick="location.href='zhuce.php'">没有账号？去注册</button>
        <button type="button" onclick="location.href='chongzhi.php'">忘记密码？重置密码</button>
    </div>
</div>
</body>
</html>
