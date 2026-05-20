<?php
session_start();
if(!isset($_SESSION['uid'])){
    header("Location: denglu.php");
    exit();
}
include 'pdo_connect.php';
// 提交留言
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $content = trim($_POST['content']);
    $uid = $_SESSION['uid'];
    $username = $_SESSION['user'];
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO messages (uid, username, content) VALUES (?, ?, ?)");
        $stmt->execute([$uid, $username, $content]);
        header('Location: user.php');
        exit();
    }
}

// 删除自己的留言
if (isset($_GET['del_msg'])) {
    $msg_id = (int)$_GET['del_msg'];
    $uid = $_SESSION['uid'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ? AND uid = ?");
    $stmt->execute([$msg_id, $uid]);

    header('Location: user.php');
    exit();
}

// 文件上传功能配置
$upload_msg = [];
$allowExts = ['jpg' => 'image/jpeg','png' => 'image/png', 'gif' => 'image/gif', 'pdf' => 'application/pdf'];
$maxSize = 20*1024 * 1024;
$maxWidth = 4000;
$maxHeight = 4000;

// 处理文件上传
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["myfile"])) {
    if (!empty($_FILES["myfile"]["name"])) {
        $filecount = count($_FILES["myfile"]["name"]);
        for ($i = 0; $i < $filecount; $i++) {
            $name = $_FILES["myfile"]["name"][$i];
            $tmpname = $_FILES["myfile"]["tmp_name"][$i];
            $mime = $_FILES["myfile"]["type"][$i];
            $filesize = $_FILES["myfile"]["size"][$i];
            if (empty($tmpname)) continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!isset($allowExts[$ext]) || $allowExts[$ext] !== $mime) {
                $upload_msg[] = "上传不合法：" . $name;
                continue;
            }
            if ($filesize > $maxSize) {
                $upload_msg[] = "文件过大：" . $name;
                continue;
            }
            $isImage = in_array($ext, ['jpg', 'png', 'gif']);
            if ($isImage) {
                $imgInfo = getimagesize($tmpname);
                $width = $imgInfo[0] ?? 0;
                $height = $imgInfo[1] ?? 0;
                if ($width > $maxWidth || $height > $maxHeight) {
                    $upload_msg[] = "图片尺寸超标：" . $name;
                    continue;
                }
            }
            $content = file_get_contents($tmpname);
            $violate_array = ["<?php","<?pHP","<?PHP","<?="];
            $safe = true;
            foreach ($violate_array as $value) {
                if (strpos($content,$value) !== false) {
                    $safe = false;
                    break;
                }
            }
            if(!$safe){
                $upload_msg[] = "文件内容不规范：" . $name;
                continue;
            }
            if (!is_dir('upload')) {
                mkdir('upload', 0777, true);
            }

            $newFileName = uniqid() . "_" . date("YmdHis") . "." . $ext;
            $uploadPath = "upload/" . $newFileName;
            if (move_uploaded_file($tmpname, $uploadPath)) {
                $upload_msg[] = "上传成功：" . $uploadPath;
            } else {
                $upload_msg[] = "上传失败：" . $name;
            }
        }
    }
}

// 读取留言
$stmt = $pdo->query("SELECT * FROM messages ORDER BY create_time DESC");
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>用户中心</title>
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

        h2, h1, h3 {
            color: #333;
            margin-bottom: 15px;
        }

        h2 {
            color: #2d8cf0;
            border-bottom: 2px solid #e8eaed;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        p {
            margin: 8px 0;
            color: #555;
        }

        a {
            color: #2d8cf0;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* 按钮样式 */
        button, input[type="submit"] {
            background-color: #2d8cf0;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover, input[type="submit"]:hover {
            background-color: #1b74d0;
        }

        /* 表单样式 */
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            font-size: 14px;
            margin-bottom: 10px;
        }

        input[type="file"] {
            margin: 10px 0;
        }

        /* 留言卡片 */
        .message-card {
            background: #fff;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .message-card strong {
            color: #2d8cf0;
            font-size: 15px;
        }

        .message-card span {
            color: #999;
            font-size: 12px;
            margin-left: 10px;
        }

        .message-card p {
            margin: 10px 0;
            color: #444;
        }

        .delete-btn {
            color: #ff4d4f !important;
            font-size: 13px;
        }

        /* 分割线 */
        hr {
            border: none;
            height: 1px;
            background: #e8eaed;
            margin: 30px 0;
        }

        /* 上传提示 */
        .upload-msg {
            padding: 6px 12px;
            border-radius: 4px;
            margin: 5px 0;
        }

        .upload-msg.success {
            background-color: #f0f9ff;
            color: #0066cc;
        }

        .upload-msg.error {
            background-color: #fff2f0;
            color: #d93025;
        }
    </style>
</head>
<body>
<h2>用户中心</h2>
<p>欢迎你：<?php echo htmlspecialchars($_SESSION['user']) ?></p>
<p><a href="xiugaimm.php">修改密码</a> | <a href="denglu.php?action=logout">退出登录</a></p>

<h1>留言板</h1>
<h3>发表留言</h3>
<form method="post">
    <textarea name="content" required placeholder="请输入留言内容..."></textarea><br>
    <button type="submit" name="submit">提交留言</button>
</form>

<h3>留言列表</h3>
<?php foreach ($messages as $msg): ?>
    <div class="message-card">
        <strong><?=htmlspecialchars($msg['username'])?></strong>
        <span><?=htmlspecialchars($msg['create_time'])?></span>
        <p><?=htmlspecialchars($msg['content'])?></p>

        <?php if ($msg['uid'] == $_SESSION['uid']): ?>
            <a href="?del_msg=<?=$msg['id']?>" onclick="return confirm('确定删除这条留言吗？')" class="delete-btn">删除留言</a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<hr>
<h1>文件上传</h1>
<h3>多文件上传</h3>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="myfile[]" multiple>
    <br><br>
    <input type="submit" value="开始上传">
</form>

<?php foreach ($upload_msg as $msg): ?>
    <?php
    $class = strpos($msg, '成功') !== false ? 'success' : 'error';
    ?>
    <p class="upload-msg <?=$class?>"><?php echo htmlspecialchars($msg) ?></p>
<?php endforeach; ?>

</body>
</html>
