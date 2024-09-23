<?php
session_start();
if (!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid'] != session_id()) {
    header("Location: login.php"); // ログインページにリダイレクト
    exit();
} else {
    session_regenerate_id(true);
    $_SESSION['chk_ssid'] = session_id();
}
?>


<?php
$host = 'xxxx';
$db = 'xxxx';
$user = 'xxxx';
$pass = 'xxxx';
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

// 保存された選択結果を取得
$query = "
    SELECT fs.id, f.store_name, f.location, f.food_name, f.calories, f.protein, f.fat, f.carbohydrates, fs.selection_time
    FROM food_selections fs
    JOIN foods f ON fs.food_id = f.id
    ORDER BY fs.selection_time DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteQuery = "DELETE FROM food_selections WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->bindParam(':id', $_POST['delete_id']);
    $deleteStmt->execute();
    header("Location: display_selections.php");
    exit();
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id']) && isset($_POST['new_time'])) {
    $updateQuery = "UPDATE food_selections SET selection_time = :selection_time WHERE id = :id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':selection_time', $_POST['new_time']);
    $updateStmt->bindParam(':id', $_POST['edit_id']);
    $updateStmt->execute();
    header("Location: display_selections.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>保存された選択結果</title>
    <style>
      table {
        width: 100%;
        border-collapse: collapse;
      }
      th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
      }
      th {
        background-color: #f2f2f2;
      }
    </style>
</head>
<body>
    <h1>保存された選択結果</h1>

    <table>
        <thead>
            <tr>
                <th>店舗名</th>
                <th>位置</th>
                <th>食品名</th>
                <th>カロリー</th>
                <th>タンパク質 (g)</th>
                <th>脂質 (g)</th>
                <th>炭水化物 (g)</th>
                <th>保存日時</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['store_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['calories']); ?></td>
                        <td><?php echo htmlspecialchars($row['protein']); ?></td>
                        <td><?php echo htmlspecialchars($row['fat']); ?></td>
                        <td><?php echo htmlspecialchars($row['carbohydrates']); ?></td>
                        <td>
                            <form method="POST">
                                <input type="text" name="new_time" value="<?php echo htmlspecialchars($row['selection_time']); ?>">
                                <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                <button type="submit">更新</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" onclick="return confirm('本当に削除しますか？')">削除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">保存された食品がありません。</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
