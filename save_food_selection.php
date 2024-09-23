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

// 日本時間を設定
date_default_timezone_set('Asia/Tokyo');

// POSTデータから選択された食品IDを取得
$selected_foods = json_decode($_POST['selected_foods'], true);

if (!empty($selected_foods)) {
    $query = "INSERT INTO food_selections (food_id, selection_time) VALUES (:food_id, :selection_time)";
    $stmt = $pdo->prepare($query);

    foreach ($selected_foods as $food_id) {
        // 現在の日本時間を取得
        $selection_time = date('Y-m-d H:i:s');

        // データベースに保存
        $stmt->bindParam(':food_id', $food_id);
        $stmt->bindParam(':selection_time', $selection_time);
        $stmt->execute();
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => '食品が選択されていません']);
}
?>
