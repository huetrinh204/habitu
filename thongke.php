<?php
session_start();

// Nếu chưa đăng nhập → chuyển về trang đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location: dangnhap.php");
    exit();
}

// Lấy username từ session
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
 
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 

</head>

<body class="bg-gradient-to-br from-cyan-300 to-teal-400 min-h-screen">

<?php include "navbar.php"; ?>



<!-- MAIN -->
<section class="container mx-auto mt-10 px-4">
  <div class="bg-white/95 rounded-3xl shadow-xl p-7 max-w-5xl mx-auto">

    <h2 class="text-xl font-semibold text-gray-800 mb-4">Thống Kê Thói Quen</h2>
    <p class="text-gray-600 mb-6">Xem tổng quan và tiến trình của bạn</p>

   <div class="grid grid-cols-3 gap-4 justify-center mb-6">
  <div class="p-4 bg-teal-100 rounded-xl flex items-center gap-3">
    <i class="fa-solid fa-list-check text-teal-700 text-xl"></i>
    <div>
      <p class="text-sm text-gray-600">Tổng thói quen</p>
      <p class="font-bold text-lg">3</p>
    </div>
  </div>

  <div class="p-4 bg-purple-100 rounded-xl flex items-center gap-3">
    <i class="fa-solid fa-bolt text-purple-700 text-xl"></i>
    <div>
      <p class="text-sm text-gray-600">Streak hiện tại</p>
      <p class="font-bold text-lg">7 ngày</p>
    </div>
  </div>

  <div class="p-4 bg-blue-100 rounded-xl flex items-center gap-3">
    <i class="fa-solid fa-percent text-blue-700 text-xl"></i>
    <div>
      <p class="text-sm text-gray-600">Hiệu suất</p>
      <p class="font-bold text-lg">62%</p>
    </div>
  </div>
</div>


    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-2 gap-6">

      <!-- Bar Chart -->
      <div class="bg-white shadow-md p-4 rounded-xl border">
        <h3 class="font-semibold text-gray-700 mb-3">Chuỗi ngày theo thói quen</h3>
        <canvas id="barChart"></canvas>
      </div>

      <!-- Pie Chart -->
      <div class="bg-white shadow-md p-4 rounded-xl border">
        <h3 class="font-semibold text-gray-700 mb-3">Tỷ lệ hoàn thành hôm nay</h3>
        <canvas id="pieChart"></canvas>
      </div>
    </div>

    <!-- LINE CHART -->
    <div class="bg-white shadow-md p-4 rounded-xl border mt-6">
      <h3 class="font-semibold text-gray-700 mb-3">Tiến độ tuần này</h3>
      <canvas id="lineChart"></canvas>
    </div>

    <!-- HABIT DETAIL LIST -->
    <div class="bg-white shadow-md p-4 rounded-xl border mt-6">
      <h3 class="font-semibold text-gray-700 mb-4">Chi tiết thói quen</h3>

      <div class="space-y-4">

        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-droplet text-blue-600"></i>
            <span>Uống 8 ly nước</span>
          </div>
          <span class="text-sm text-gray-500">5/7 ngày</span>
        </div>

        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-running text-green-600"></i>
            <span>Tập thể dục 20 phút</span>
          </div>
          <span class="text-sm text-gray-500">3/7 ngày</span>
        </div>

        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <i class="fa-solid fa-moon text-yellow-500"></i>
            <span>Thiền 10 phút</span>
          </div>
          <span class="text-sm text-gray-500">6/7 ngày</span>
        </div>

      </div>
    </div>

  </div>
</section>



<!-- CHART SCRIPTS -->
<script>
  // BAR CHART
  new Chart(document.getElementById("barChart"), {
    type: "bar",
    data: {
      labels: ["Uống nước", "Tập thể dục", "Thiền"],
      datasets: [{
        label: "Số ngày duy trì",
        data: [20, 12, 28],
        backgroundColor: ["#14b8a6", "#3b82f6", "#f97316"]
      }]
    },
    options: { responsive: true }
  });

  // PIE CHART
  new Chart(document.getElementById("pieChart"), {
    type: "pie",
    data: {
      labels: ["Đã hoàn thành", "Chưa hoàn thành"],
      datasets: [{
        data: [35, 65],
        backgroundColor: ["#10b981", "#f97316"]
      }]
    }
  });

  // LINE CHART
  new Chart(document.getElementById("lineChart"), {
    type: "line",
    data: {
      labels: ["T2", "T3", "T4", "T5", "T6", "T7", "CN"],
      datasets: [{
        label: "Thói quen hoàn thành",
        data: [3, 4, 2, 5, 4, 3, 2],
        borderColor: "#14b8a6",
        fill: false,
        tension: 0.3
      }]
    }
  });
</script>

</body>
</html>
