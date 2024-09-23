<?php
// データベース接続設定
$host = 'xxxx';
$db = 'xxxx';
$user = 'xxxx';
$pass = 'xxxx';
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
?>
