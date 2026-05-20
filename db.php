<?php
include 'pdo_connect.php';
?>
//$sql = "CREATE TABLE IF NOT EXISTS students(
//id INT AUTO_INCREMENT PRIMARY KEY,
//name VARCHAR(30) NOT NULL,
//age INT NOT NULL
//)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
//$result = $pdo->exec($sql);
//if ($result !== false) {
//    echo "创建成功";
//}else{
//    $error = $pdo->errorInfo();
//    echo "创建失败".$error[2];
//}


//$name = "张三";
//$age = 18;
//$sql = "INSERT INTO students (name, age) VALUES (:name,:age)";
//$stmt = $pdo->prepare($sql);
//$result = $stmt->execute(['name'=>$name, 'age'=>$age]);
//if ($result) {
//    echo "数据插入成功".$pdo->lastInsertId();
//}else{
//    $error = $stmt->errorInfo();
//    echo "插入失败".$error[2];
//}


//if ($_POST) {
//    $name = $_POST['name'];
//    $age = $_POST['age'];
//
//    $sql = "INSERT INTO students (name, age) VALUES (:name, :age)";
//    $stmt = $pdo->prepare($sql);
//    $result = $stmt->execute([
//        ':name' => $name,
//        ':age' => $age
//    ]);
//
//    if ($result) {
//        echo "数据插入成功！新建ID为：" . $pdo->lastInsertId();
//    } else {
//        $error = $stmt->errorInfo();
//        echo "插入失败：" . $error[2];
//    }
//}

//<!--<h2>添加学生信息</h2>-->
//<!--<form method="post">-->
//<!--    姓名：<input type="text" name="name" required><br><br>-->
//<!--    年龄：<input type="number" name="age" required><br><br>-->
//<!--    <button type="submit">提交添加</button>-->
//<!--</form>-->


//$sql = "CREATE TABLE IF NOT EXISTS users(
//id INT AUTO_INCREMENT PRIMARY KEY,
//username VARCHAR(255) NOT NULL UNIQUE,
//password VARCHAR(255) NOT NULL,
//email VARCHAR(255) NOT NULL UNIQUE
//)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
//$result = $pdo->exec($sql);
//if ($result !== false) {
//    echo "创建成功";
//}else{
//    $error = $pdo->errorInfo();
//    echo "创建失败".$error[2];
//}


//更新
//$new_age = "25";
//$select_id = "1";
//$sql = "UPDATE students SET age = :new_age WHERE id = :id";
//$stmt = $pdo->prepare($sql);
//$stmt->execute([
//    'new_age' =>$new_age,
//    'id' => $select_id
//]);
//if($stmt){
//    echo "成功更新id为".$select_id."条目";
//}else{
//    $error = $pdo->errorInfo();
//    echo "更新失败";
//}


//删除
//$select_id ="2";
//$sql = "DELLECT FROM students WHERE id = :id";
//$stmt = $pdo->prepare($sql);
//$stmt->execute(['id' => $select_id]);
//if($stmt){
//    echo "删除成功";
//}else{
//}

<a href="https://www.bin.com" onclick="return confirm('确定要打开吗？')">bing</a>
<form action="" method="post" onsubmit="return confirm('确定要打开吗？')">
    <input name="a" type="text">
    <input type="submit">
</form>



