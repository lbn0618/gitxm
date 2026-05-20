<?php
session_start();
include 'pdo_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    $username = trim($_POST['username'] ?? '');
    $pwd = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']) ? 1 : 0;
    $response = ['code' => 1, 'msg' => '账号或密码错误'];

    // 验证图形验证码
    $captcha = strtolower(trim($_POST['captcha'] ?? ''));
    if (empty($captcha) || $captcha !== ($_SESSION['captcha'] ?? '')) {
        $response = ['code' => 1, 'msg' => '验证码错误'];
        echo json_encode($response);
        exit;
    }

    if (!empty($username) && !empty($pwd)) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($pwd, $user['password'])) {
            if ($remember) {
                setcookie('remembered_username', $username, time() + 7 * 24 * 60 * 60, '/');
            } else {
                setcookie('remembered_username', '', time() - 3600, '/');
            }

            $_SESSION['uid'] = $user['id'];
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            // 清除验证码session
            unset($_SESSION['captcha']);
            $response = [
                    'code' => 0,
                    'msg' => '登录成功',
                    'role' => $user['role'],
                    'redirect' => $user['role'] === 'admin' ? 'admin.php' : 'user.php'
            ];
        }
    }
    echo json_encode($response);
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = [];
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
$remembered_username = $_COOKIE['remembered_username'] ?? '';
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
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* 浮动粒子效果 */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        .particle:nth-child(1) { width: 80px; height: 80px; left: 10%; animation-duration: 20s; animation-delay: 0s; }
        .particle:nth-child(2) { width: 60px; height: 60px; left: 20%; animation-duration: 25s; animation-delay: 2s; }
        .particle:nth-child(3) { width: 40px; height: 40px; left: 35%; animation-duration: 22s; animation-delay: 4s; }
        .particle:nth-child(4) { width: 70px; height: 70px; left: 50%; animation-duration: 28s; animation-delay: 1s; }
        .particle:nth-child(5) { width: 50px; height: 50px; left: 65%; animation-duration: 24s; animation-delay: 3s; }
        .particle:nth-child(6) { width: 90px; height: 90px; left: 80%; animation-duration: 26s; animation-delay: 5s; }
        .particle:nth-child(7) { width: 30px; height: 30px; left: 90%; animation-duration: 21s; animation-delay: 2s; }
        .particle:nth-child(8) { width: 55px; height: 55px; left: 15%; animation-duration: 23s; animation-delay: 6s; }
        .particle:nth-child(9) { width: 45px; height: 45px; left: 45%; animation-duration: 27s; animation-delay: 4s; }
        .particle:nth-child(10) { width: 65px; height: 65px; left: 75%; animation-duration: 19s; animation-delay: 1s; }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.5;
            }
            90% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
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
            display: none;
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

        button:disabled {
            background: #ccc !important;
            cursor: not-allowed !important;
        }

        /* 验证码样式 */
        .captcha-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .captcha-row input {
            flex: 1;
        }

        .captcha-img {
            height: 44px;
            border-radius: 6px;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .captcha-img:hover {
            opacity: 0.8;
        }


    </style>
</head>
<body>
<!-- 动态粒子背景 -->
<div class="particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>
<div class="login-box">
    <h2>用户登录</h2>
    <div class="error-msg" id="msg"></div>
    <form method="post" id="loginForm">
        <p>用户名：<input type="text" name="username" id="username" value="<?php echo htmlspecialchars($remembered_username); ?>"></p>
        <p>密　码：<input type="password" name="password" id="password"></p>
        <p>验证码：<span class="captcha-row"><input type="text" name="captcha" id="captcha" placeholder="请输入验证码" maxlength="4">
        <span class="text-captcha" id="textCaptcha" title="点击刷新" style="font-size:24px;font-weight:bold;letter-spacing:5px;color:#e74c3c;background:linear-gradient(135deg,#f5f5f5,#e0e0e0);padding:5px 15px;border-radius:5px;cursor:pointer;user-select:none;font-family:Arial,sans-serif;text-shadow:1px 1px 2px rgba(0,0,0,0.1);">加载中...</span></span></p>

        <p class="remember">
            <input type="checkbox" name="remember" id="remember" <?php echo $remembered_username ? 'checked' : ''; ?>>
            <label for="remember">记住用户名</label>
        </p>
        <button type="submit" id="loginBtn">登录</button>
    </form>
    <div class="btn-group">
        <button type="button" onclick="location.href='zhuce.php'">没有账号？去注册</button>
        <button type="button" onclick="location.href='chongzhi.php'">忘记密码？重置密码</button>
    </div>
</div>
<script>
    // 文本验证码加载函数
    function loadCaptcha() {
        fetch('captcha_text.php?t=' + Date.now())
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('textCaptcha').textContent = data.code;
                } else {
                    document.getElementById('textCaptcha').textContent = '错误';
                }
            })
            .catch(err => {
                document.getElementById('textCaptcha').textContent = '加载失败';
            });
    }

    // 文本验证码刷新
    const textCaptcha = document.getElementById('textCaptcha');
    textCaptcha.onclick = function() {
        loadCaptcha();
    };

    // 页面加载时获取验证码
    loadCaptcha();

    //监听
    const form = document.getElementById('loginForm');
    const msgBox = document.getElementById('msg');
    const loginBtn = document.getElementById('loginBtn');
    form.addEventListener('submit', async (e) => {
        e.preventDefault(); // 阻止默认刷新提交
        loginBtn.disabled = true;
        loginBtn.textContent = '登录中...';
        msgBox.style.display = 'none';
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        const captcha = document.getElementById('captcha').value.trim();
        const remember = document.getElementById('remember').checked ? 1 : 0;

        // 前端验证
        if (!captcha) {
            msgBox.innerText = '请输入验证码';
            msgBox.style.display = 'block';
            loginBtn.disabled = false;
            loginBtn.textContent = '登录';
            return;
        }


        //发送请求
        try {
            const res = await fetch('denglu.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `ajax=1&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&captcha=${encodeURIComponent(captcha)}&remember=${remember}`
            });
            //处理
            const data = await res.json();
            if (data.code === 0) {
                msgBox.innerText = data.msg;
                msgBox.style.color = '#009688';
                msgBox.style.background = '#e8f5e9';
                msgBox.style.display = 'block';
                setTimeout(() => {
                    location.href = data.redirect;
                }, 800);
            } else {
                msgBox.innerText = data.msg;
                msgBox.style.display = 'block';
                // 刷新验证码
                loadCaptcha();
            }
        } catch (err) {
            msgBox.innerText = '网络异常，请重试';
            msgBox.style.display = 'block';
        } finally {
            loginBtn.disabled = false;
            loginBtn.textContent = '登录';
        }
    });
</script>
</body>
</html>











































