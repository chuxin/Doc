<?php
require_once "../vendor/autoload.php";
use Doctrine\DBAL\DriverManager;
$connectionParams = array(
    'dbname' => 'test',
    'user' => 'root',
    'password' => 'toplfx007',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);
$conn = DriverManager::getConnection($connectionParams);

//$sql = "SELECT * FROM album";
//$stmt = $conn->query($sql);

//foreach ($stmt as $key => $value) {
//    echo $value['title'];
//}


//用来获取一条记录
//while ($row = $stmt->fetch()) {
//    echo $row['title'];
//}

//获取所有记录集到一个数组中
//print_r($stmt->fetchAll());

// 获取结果指定第一条记录的某个字段，缺省是第一个字段。
//while ($row = $stmt->fetchColumn(2)) {
//    echo $row;
//}

$id=2;$title='21';
$sql = "SELECT * FROM album WHERE id = ? AND title = ?";

//$stmt = $conn->prepare($sql);
////绑定第一个参数
//$stmt->bindValue(1, $id);
////绑定第二个参数
//$stmt->bindValue(2, $title);
//$stmt->execute();

//执行查询绑定   ---executeUpdate()  第三个参数是绑定类型,第四个参数是查询缓存
//$stmt = $conn->executeQuery($sql, array($id,$title));

$row  = $stmt->fetch();
echo $row['artist'];


$count = $conn->executeUpdate('UPDATE user SET username = ? WHERE id = ?', array('jwage', 1));
echo $count; // 1

$user = $conn->fetchArray('SELECT * FROM user WHERE username = ?', array('jwage'));

$username = $conn->fetchColumn('SELECT username FROM user WHERE id = ?', array(1), 0);
echo $username; // jwage


$user = $conn->fetchAssoc('SELECT * FROM user WHERE username = ?', array('jwage'));

// DELETE FROM user WHERE id = ? (1)
$conn->delete('user', array('id' => 1));

// INSERT INTO user (username) VALUES (?) (jwage)
$conn->insert('user', array('username' => 'jwage'));

// UPDATE user (username) VALUES (?) WHERE id = ? (jwage, 1)
$conn->update('user', array('username' => 'jwage'), array('id' => 1));

//引号
$quoted = $conn->quote('value');
$quoted = $conn->quote('1234', \PDO::PARAM_INT);

//通过平台引用一个值
$quoted = $conn->quoteIdentifier('id');

//安全处理
// SQL Prepared Statements: Positional
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $connection->prepare($sql);
$stmt->bindValue(1, $_GET['username']);
$stmt->execute();

// SQL Prepared Statements: Named
$sql = "SELECT * FROM users WHERE username = :user";
$stmt = $connection->prepare($sql);
$stmt->bindValue("user", $_GET['username']);
$stmt->execute();

// bind parameters and execute query at once.
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $connection->executeQuery($sql, array($_GET['username']));

$sql = "SELECT * FROM users WHERE name = " . $connection->quote($_GET['username'], \PDO::PARAM_STR);




