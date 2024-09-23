let chartInstance;
let googleMap;  // Google Mapsオブジェクト
let markers = [];  // マーカーを保存する配列

// 食品データを取得してテーブルに表示する
function fetchFoodData(lat, lng, distance) {
  fetch('fetch_food_data.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `lat=${lat}&lng=${lng}&distance=${distance}`,
  })
    .then((response) => response.json())
    .then((data) => {
      const list = document.getElementById('food-list');
      list.innerHTML = '';

      // テーブルのヘッダーを追加
      const table = document.createElement('table');
      table.innerHTML = `
        <thead>
          <tr>
            <th>店舗名</th>
            <th>食品名</th>
            <th>カロリー</th>
            <th>タンパク質 (g)</th>
            <th>脂質 (g)</th>
            <th>炭水化物 (g)</th>
            <th>選択</th>
          </tr>
        </thead>
        <tbody></tbody>
      `;

      const tbody = table.querySelector('tbody');

      // 各食品データをテーブルの行として追加
      data.forEach((item) => {
        const location = item.location.split(',');  // 位置情報を取得（緯度・経度）
        const lat = parseFloat(location[0]);
        const lng = parseFloat(location[1]);

        if (!isNaN(lat) && !isNaN(lng)) {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${item.store_name}</td>
            <td>${item.food_name}</td>
            <td>${item.calories}</td>
            <td>${item.protein}</td>
            <td>${item.fat}</td>
            <td>${item.carbohydrates}</td>
            <td><input type="checkbox" value="${item.id}" data-id="${item.id}" data-lat="${lat}" data-lng="${lng}" data-calories="${item.calories}" data-protein="${item.protein}" data-fat="${item.fat}" data-carbohydrates="${item.carbohydrates}"> 選択</td>
          `;
          tbody.appendChild(row);
        }
      });

      list.appendChild(table);
      setupCheckboxes();  // チェックボックス設定
    })
    .catch((error) => {
      console.error('データの取得中にエラーが発生しました:', error);
      document.getElementById('food-list').innerHTML = '<p>食品データの取得に失敗しました。</p>';
    });
}

// チェックボックスの操作に応じてマーカーを追加・削除し、グラフを更新する
function setupCheckboxes() {
  const checkboxes = document.querySelectorAll('input[type="checkbox"]');
  let selectedFoods = [];

  checkboxes.forEach((box) => {
    box.addEventListener('change', function () {
      const foodId = parseInt(this.dataset.id);
      const lat = parseFloat(this.dataset.lat);
      const lng = parseFloat(this.dataset.lng);
      const calories = parseFloat(this.dataset.calories);
      const protein = parseFloat(this.dataset.protein);
      const fat = parseFloat(this.dataset.fat);
      const carbs = parseFloat(this.dataset.carbohydrates);

      console.log(`チェックボックスの変更: ID=${foodId}, 緯度=${lat}, 経度=${lng}`);  // デバッグ用

      if (this.checked) {
        // チェックボックスがチェックされた場合、食品情報を追加し、マーカーを追加
        selectedFoods.push({ id: foodId, calories, protein, fat, carbs });
        addMarker(lat, lng);  // マーカーを追加
      } else {
        // チェックボックスが外された場合、食品情報をリストから削除し、マーカーを削除
        selectedFoods = selectedFoods.filter(food => food.id !== foodId);
        removeMarker(lat, lng);  // マーカーを削除
      }

      // チェックされた食品の情報でグラフを更新
      updateChart(selectedFoods);
    });
  });

  // 保存ボタンが押されたら、選択した食品をサーバーに送信
  document.getElementById('save-selection').addEventListener('click', function () {
    if (selectedFoods.length > 0) {
      const foodIds = selectedFoods.map(food => food.id);  // サーバーに送るのはidのみ
      saveFoodSelections(foodIds);
    } else {
      alert("食品を選択してください");
    }
  });
}

// Google Mapsにマーカーを追加する
function addMarker(lat, lng) {
  if (!googleMap) {
    console.error('Google Mapが初期化されていません');
    return;
  }

  console.log(`マーカー追加: 緯度=${lat}, 経度=${lng}`);  // デバッグ用

  const marker = new google.maps.Marker({
    position: { lat: lat, lng: lng },
    map: googleMap,  // googleMapオブジェクトを使用
  });
  markers.push(marker);  // マーカーを配列に保存
}

// Google Mapsからマーカーを削除する
function removeMarker(lat, lng) {
  if (!googleMap) {
    console.error('Google Mapが初期化されていません');
    return;
  }

  console.log(`マーカー削除: 緯度=${lat}, 経度=${lng}`);  // デバッグ用

  markers = markers.filter(marker => {
    const position = marker.getPosition();
    if (position.lat() === lat && position.lng() === lng) {
      marker.setMap(null);  // マーカーを削除
      return false;
    }
    return true;
  });
}

// Google Mapsの初期化
function initMap() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (position) {
      const pos = { lat: position.coords.latitude, lng: position.coords.longitude };

      googleMap = new google.maps.Map(document.getElementById('map'), {
        center: pos,
        zoom: 15,
      });

      new google.maps.Marker({
        position: pos,
        map: googleMap,  // googleMapを使用
        title: '現在地',
      });

      console.log("Google Mapが初期化されました");  // デバッグ用
    });
  } else {
    alert('位置情報がサポートされていません');
  }
}

// 選択した食品を保存するためのPOSTリクエストをサーバーに送信
function saveFoodSelections(selectedFoods) {
  fetch('save_food_selection.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `selected_foods=${JSON.stringify(selectedFoods)}`
  })
  .then((response) => response.json())
  .then((data) => {
    if (data.status === 'success') {
      alert('選択した食品が保存されました');
    } else {
      alert('保存に失敗しました');
    }
  });
}

// グラフを更新する
function updateChart(selectedFoods) {
  // 合計値を計算
  const totalCalories = selectedFoods.reduce((acc, food) => acc + food.calories, 0);
  const totalProtein = selectedFoods.reduce((acc, food) => acc + food.protein, 0);
  const totalFat = selectedFoods.reduce((acc, food) => acc + food.fat, 0);
  const totalCarbs = selectedFoods.reduce((acc, food) => acc + food.carbs, 0);

  // PFCからのカロリー計算
  const proteinCalories = totalProtein * 4;  // 1gあたり4kcal
  const fatCalories = totalFat * 9;          // 1gあたり9kcal
  const carbsCalories = totalCarbs * 4;      // 1gあたり4kcal

  const ctx = document.getElementById('pfcChart').getContext('2d');

  // 既存のチャートがある場合は更新し、なければ新規作成
  if (chartInstance) {
    chartInstance.data.datasets[0].data = [proteinCalories, fatCalories, carbsCalories];
    chartInstance.update();
  } else {
    chartInstance = new Chart(ctx, {
      type: 'doughnut',  // 円グラフに変更
      data: {
        labels: ['Protein (Calories)', 'Fat (Calories)', 'Carbs (Calories)'],
        datasets: [{
          label: 'PFCのカロリー',
          data: [proteinCalories, fatCalories, carbsCalories],
          backgroundColor: ['blue', 'yellow', 'green'],
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          }
        }
      }
    });
  }

  // 合計カロリーをテキストで表示
  const totalCaloriesText = document.getElementById('total-calories');
  totalCaloriesText.textContent = `合計カロリー: ${totalCalories} kcal`;
}

// 初期設定、DOMが読み込まれたときに実行される
document.addEventListener('DOMContentLoaded', () => {
  // 初期表示では何もせず、ユーザーが距離を入力して更新ボタンを押す
});
