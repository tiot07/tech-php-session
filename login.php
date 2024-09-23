<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログインページ</title>
</head>
<body>
    <h1>ログインフォーム</h1>
    <form action="login_act.php" method="post">
        <label for="lid">ユーザーID:</label>
        <input type="text" id="lid" name="lid">
        <br>
        <label for="lpw">パスワード:</label>
        <input type="password" id="lpw" name="lpw">
        <br>
        <button type="submit">ログイン</button>
    </form>
    <a href="user_register.php">ユーザー登録</a>

</body>
</html>
