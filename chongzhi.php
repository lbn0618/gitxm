<?php
include 'pdo_connect.php';
$msg = '';
if($_POST){
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $new_pwd  = trim($_POST['new_pwd']);

    if(empty($username) || empty($email) || empty($new_pwd)){
        $msg = "用户名、邮箱、新密码不能为空！";
    }else{
        $sql = "SELECT id FROM users WHERE username=:username AND email=:email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username'=>$username,':email'=>$email]);
        $row = $stmt->fetch();
        if(!$row){
            $msg = "用户名与邮箱不匹配！";
        }else{
            $new_hash = password_hash($new_pwd, PASSWORD_DEFAULT);
            $up_stmt = $pdo->prepare("UPDATE users SET password=:pwd WHERE id=:id");
            $res = $up_stmt->execute([':pwd'=>$new_hash,':id'=>$row['id']]);
            $msg = $res ? "密码重置成功！" : "重置失败！";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>重置密码</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Microsoft YaHei", sans-serif;
        }

        body {
            background-color: #f5f7fa;
            padding: 30px 20px;
            max-width: 500px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .msg {
            text-align: center;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .msg:empty {
            display: none;
        }

        form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        form p {
            margin-bottom: 18px;
        }

        form label {
            display: inline-block;
            width: 80px;
            font-size: 14px;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 90px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            font-size: 14px;
        }

        input:focus {
            border-color: #409eff;
        }

        .btn-box {
            margin-top: 25px;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: #409eff;
            font-size: 14px;
        }

        .submit-btn {
            display: inline-block;
            background: #409eff;
            color: #fff;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            margin-right: 10px;
        }

        .submit-btn:hover {
            background: #338eef;
        }
    </style>
</head>
<body>
<h2>重置密码</h2>
<div class="msg"><?php echo $msg; ?></div>

<form method="post" id="form" onsubmit="return checkForm()">
    <p>
        <label>用户名：</label>
        <input type="text" name="username">
    </p>
    <p>
        <label>邮箱：</label>
        <input type="text" name="email">
    </p>
    <p>
        <label>新密码：</label>
        <input type="password" name="new_pwd">
    </p>

    <div class="btn-box">
        <button type="submit" class="submit-btn">确认重置</button>
        <a href="denglu.php">返回登录</a>
    </div>
</form>

<script>
    function checkForm() {
        if(!confirm('确定要重置密码吗？')) {
            return false;
        }
        return true;
    }
</script>
</body>
</html>