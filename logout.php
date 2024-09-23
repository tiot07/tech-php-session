<?php
session_start();
// セッション変数をすべて削除
$_SESSION = array();

// クッキーに保存しているセッションIDを削除
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// セッションを破棄
session_destroy();
header("Location: login.php");
exit();
?>
