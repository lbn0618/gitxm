<?php
include 'pdo_connect.php';
try {
    $sql1 = "ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user' COMMENT '身份：admin=管理员，user=普通用户'";
    $pdo->exec($sql1);
    echo "字段添加成功<br>";
    $sql2 = "UPDATE users SET role='admin' WHERE username='admin'";
    $pdo->exec($sql2);
    echo "管理员设置成功";

} catch (PDOException $e) {
    echo "信息：" . $e->getMessage();
}
