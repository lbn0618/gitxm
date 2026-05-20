<?php
include 'pdo_connect.php';
//$name = "";
//$age = "";
//$sql = "SELECT * FROM students";
//$stmt = $pdo->query($sql);
//if ($stmt->rowCount() > 0) {
//    while ($row = $stmt->fetch()) {
//        echo "id: " . $row["id"] . "name".$row["name"]. "age".$row["age"]. "<br>";
//    }
//}else{
//    echo "数据库没有数据";
//}


$selrct_name = "张三";
$sql = "SELECT * FROM students WHERE name = :name";
$stmt = $pdo->prepare($sql);
$stmt->execute([':name' => $selrct_name]);
$result = $stmt->fetchAll();
if($result){
    echo "查询到了".count($result)."条记录".'<br>';
    foreach ($result as $row){
        echo $row['id']."<br>";
        echo $row['name']."<br>";
        echo $row['age']."<br>";
    }
}else {
    echo "查无此人";
}