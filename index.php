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

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>食品情報表示サイト</title>
    
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=xxxx&callback=initMap" async defer></script>
    
    <!-- CSS スタイル -->
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
    
    <!-- 初期化関数 -->
    <script>
      let map, currentLat, currentLng;

      function initMap() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function (position) {
            currentLat = position.coords.latitude;
            currentLng = position.coords.longitude;
            const pos = {
              lat: currentLat,
              lng: currentLng,
            };
            map = new google.maps.Map(document.getElementById('map'), {
              center: pos,
              zoom: 15,
            });
            new google.maps.Marker({
              position: pos,
              map: map,
              title: '現在地',
            });
          });
        } else {
          alert('位置情報を取得できません');
        }
      }

      function updateFoodList() {
        const distance = document.getElementById('distance').value;
        if (!currentLat || !currentLng) {
          alert("位置情報を取得できていません");
          return;
        }
        fetchFoodData(currentLat, currentLng, distance);
      }
    </script>
  </head>

  <body>
    <h3>現在地を表示</h3>
    <div id="map" style="height: 500px; width: 100%;"></div>

    <h3>距離を指定して食品リストを表示</h3>
    <label for="distance">距離 (km):</label>
    <input type="number" id="distance" value="5" step="0.1" min="0">
    <button onclick="updateFoodList()">リストを更新</button>

    <h3>食品リスト</h3>
    <div id="food-list"></div>

    <!-- ユーザー情報表示 -->
    <p>ログイン中のユーザー: <strong><?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <p>ユーザー種別: 
      <strong>
        <?php 
        if ($_SESSION['kanri_flg'] == 1) {
          echo "管理者";
        } else {
          echo "一般ユーザー";
        }
        ?>
      </strong>
    </p>

    <!-- 保存ボタン -->
    <button id="save-selection">選択した食品を保存</button>

    <!-- 保存された選択結果ページへのリンクを追加 -->
    <a href="display_selections.php">保存された選択結果を表示</a>
    <br>
    <a href="logout.php">ログアウト</a> <!-- ログアウトリンク -->

    <!-- グラフ表示用のキャンバス -->
    <canvas id="pfcChart" width="400" height="400"></canvas>

     <!-- 合計カロリー表示用のテキスト -->
     <p id="total-calories"></p>

    <!-- Chart.jsライブラリ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- JavaScriptファイル -->
    <script src="main.js"></script>
  </body>
</html>
