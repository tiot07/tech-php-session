<?php
session_start();
include('db_connect.php'); // データベース接続ファイル

// フォームから送信されたデータを受け取る
$name = $_POST['name'];
$lid = $_POST['lid'];
$lpw = $_POST['lpw'];
$kanri_flg = $_POST['kanri_flg'];

// パスワードをハッシュ化
$hashed_lpw = password_hash($lpw, PASSWORD_DEFAULT);

// データベースに新しいユーザーを登録
$stmt = $pdo->prepare("INSERT INTO gs_user_table (name, lid, lpw, kanri_flg, life_flg) 
VALUES (:name, :lid, :lpw, :kanri_flg, 0)");

$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->bindValue(':lpw', $hashed_lpw, PDO::PARAM_STR);
$stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT);
$stmt->execute();

header("Location: user_register.php"); // 登録成功後に再度登録フォームへリダイレクト
exit();
?>
