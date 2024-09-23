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


$current_lat = $_POST['lat'];
$current_lng = $_POST['lng'];
$distance_limit = $_POST['distance'];

$query = "
    SELECT id, store_name, location, food_name, calories, protein, fat, carbohydrates,
    (6371 * acos(cos(radians(:lat)) * cos(radians(SUBSTRING_INDEX(location, ',', 1))) 
    * cos(radians(SUBSTRING_INDEX(location, ',', -1)) - radians(:lng)) 
    + sin(radians(:lat)) * sin(radians(SUBSTRING_INDEX(location, ',', 1))))) AS distance
    FROM foods
    HAVING distance < :distance_limit
    ORDER BY distance;
";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':lat', $current_lat);
$stmt->bindParam(':lng', $current_lng);
$stmt->bindParam(':distance_limit', $distance_limit);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
?>
