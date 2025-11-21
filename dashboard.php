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

<body class="bg-gradient-to-br from-cyan-300 to-teal-400 min-h-screen">

<!-- NAV -->
<?php include "navbar.php"; ?>

<!-- Header -->
<header class="welcome text-center py-6">
  <h2 class="text-2xl font-semibold text-gray-700" style="color: white;">Chào mừng bạn đến với Habitu! 🐱✨</h2>
  <p class="text-gray-600">Hãy tiếp tục xây dựng thói quen lành mạnh của bạn!</p>
</header>

<!-- Stats -->
<section class="stats container mx-auto grid grid-cols-3 gap-6 px-6">
  <div class="stat-box bg-white shadow-md p-5 rounded-lg flex items-center gap-4">
    <img style="border-radius: 60%; width: 50px; height : 50px;" src="assets/icons/sun.png" class="w-10" alt="icon">
    <div>
      <h3 class="font-semibold">Tổng thói quen</h3>
      <p class="text-lg font-bold">3</p>
    </div>
  </div>

  <div class="stat-box bg-white shadow-md p-5 rounded-lg flex items-center gap-4">
    <img style="border-radius: 60%; width: 50px; height : 50px;" src="assets/icons/check.png" class="w-10" alt="icon">
    <div>
      <h3 class="font-semibold">Hoàn thành hôm nay</h3>
      <p class="text-lg font-bold">1/3 (33%)</p>
    </div>
  </div>

  <div class="stat-box bg-white shadow-md p-5 rounded-lg flex items-center gap-4">
    <img style="border-radius: 60%; width: 50px; height : 50px;" src="assets/icons/streak.png" class="w-10" alt="icon">
    <div>
      <h3 class="font-semibold">Tổng chuỗi ngày</h3>
      <p class="text-lg font-bold">15 ngày</p>
    </div>
  </div>
</section>

<!-- Habits -->
<section class="habits-section px-6 mt-8">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold text-gray-800">Thói Quen Của Bạn</h3>
    <button id="addHabitBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
      + Thêm Thói Quen
    </button>
  </div>

  <div id="habitList" class="habit-list">
    <!-- render habits -->
  </div>
</section>

<script src="./assets/js/dashboard.js"></script>
</body>
</html>
