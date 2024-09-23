<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
</head>
<body>
    <h1>ユーザー登録フォーム</h1>
    <form action="user_register_act.php" method="post">
        <label for="name">名前:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="lid">ログインID:</label>
        <input type="text" id="lid" name="lid" required>
        <br>
        <label for="lpw">パスワード:</label>
        <input type="password" id="lpw" name="lpw" required>
        <br>
        <label for="kanri_flg">ユーザー種別:</label>
        <select id="kanri_flg" name="kanri_flg">
            <option value="0">一般ユーザー</option>
            <option value="1">管理者</option>
        </select>
        <br>
        <button type="submit">登録</button>
    </form>
    <br>
    <a href="index.php">ホームに戻る</a>
</body>
</html>
