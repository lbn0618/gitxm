<?php
include 'pdo_connect.php';
$msg = '';
if ($_POST) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email    = trim($_POST['email']);
    if(empty($username) || empty($password) || empty($email)){
        $msg = "<div class='msg error'>账号、密码、邮箱不能为空！</div>";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "<div class='msg error'>邮箱格式不正确！</div>";
    }
    else {
        $check_user = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $check_user->execute([':username' => $username]);
        if ($check_user->rowCount() > 0) {
            $msg = "<div class='msg error'>用户名已存在</div>";
        }else{
            $check_email = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $check_email->execute([':email' => $email]);
            if ($check_email->rowCount() > 0) {
                $msg = "<div class='msg error'>邮箱已被注册</div>";
            }else{
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    ':username' => $username,
                    ':password' => $password_hash,
                    ':email'    => $email
                ]);
                if ($result) {
                    $msg = "<div class='msg success'>注册成功！请去登录</div>";
                } else {
                    $msg = "<div class='msg error'>注册失败！</div>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户注册</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Microsoft YaHei", sans-serif;
        }

        body {
            background-color: #f5f7fa;
            padding: 30px 15px;
            max-width: 450px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
        }

        /* 提示消息样式 */
        .msg {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
        }

        .msg.error {
            background-color: #fef0f0;
            color: #f56c6c;
            border: 1px solid #fde2e2;
        }

        .msg.success {
            background-color: #f0f9ff;
            color: #409eff;
            border: 1px solid #e1f3ff;
        }

        /* 表单样式 */
        form {
            background: #fff;
            padding: 30px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        }

        form div {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
        }

        input {
            width: 100%;
            height: 40px;
            padding: 0 12px;
            border: 1px solid #dcdfe6;
            border-radius: 4px;
            outline: none;
            font-size: 14px;
            transition: border 0.3s;
        }

        input:focus {
            border-color: #409eff;
        }

        /* 按钮样式 */
        button {
            width: 100%;
            height: 42px;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button[type="submit"] {
            background-color: #409eff;
            color: #fff;
            margin-bottom: 10px;
        }

        button[type="submit"]:hover {
            background-color: #337ecc;
        }

        button[type="button"] {
            background-color: #fff;
            color: #666;
            border: 1px solid #dcdfe6;
        }

        button[type="button"]:hover {
            background-color: #f5f7fa;
        }
    </style>
</head>
<body>
<h2>用户注册</h2>
<?php if(!empty($msg)) echo $msg; ?>
<form method="post">
    <div>
        <label>用户名</label>
        <input type="text" name="username" required>
    </div>
    <div>
        <label>密码</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label>邮箱</label>
        <input type="text" name="email" required>
    </div>
    <button type="submit">立即注册</button>
    <button type="button" onclick="location.href='denglu.php'">返回登录</button>
</form>
</body>
</html>
