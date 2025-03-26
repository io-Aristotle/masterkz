<?php
$host = 'localhost';
$user = 'root';
$password = '';  // пароль базы данных
$dbname = 'masterkz';
$conn = new mysqli($host, $user, $password, $dbname, 3307);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
?>
