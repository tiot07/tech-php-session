<?php
session_start();
include('db_connect.php'); // データベース接続ファイル

$lid = $_POST['lid'];
$lpw = $_POST['lpw'];

// データベースからユーザー情報を取得
$stmt = $pdo->prepare("SELECT * FROM gs_user_table WHERE lid=:lid");
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ユーザーが存在し、パスワードが一致するか確認
if ($user && password_verify($lpw, $user['lpw'])) {
    session_regenerate_id(true);
    $_SESSION['chk_ssid'] = session_id();
    $_SESSION['kanri_flg'] = $user['kanri_flg'];
    $_SESSION['name'] = $user['name'];
    header("Location: index.php"); // ログイン成功後にリダイレクト
} else {
    echo "ログインに失敗しました。";
    header("Location: login.php");
    exit();
}
