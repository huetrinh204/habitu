
<?php
session_start();
include "config.php";

// Nếu chưa đăng nhập → chuyển về trang đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location: dangnhap.php");
    exit();
}

// Lấy username từ session
$username = $_SESSION["username"];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT * FROM habit 
    WHERE status='Mẫu' OR (status='Người dùng' AND user_id=:user_id) 
    ORDER BY created_hb DESC
");
$stmt->execute(['user_id' => $user_id]);
$habits = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<?php foreach ($habits as $hb): ?>
    <div class="habit-item bg-white p-4 rounded-xl shadow-md mb-3 flex items-center gap-3">

        <!-- Checkbox hoàn thành -->
        <input type="checkbox" class="habit-checkbox" 
            data-habit-id="<?= $hb['habit_id'] ?>"
          

        <!-- Icon -->
        <div class="text-3xl"><?= htmlspecialchars($hb['icon']) ?></div>

        <!-- Tên + miêu tả -->
        <div>
            <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($hb['habit_name']) ?></h4>
            <p class="text-gray-500 text-sm"><?= htmlspecialchars($hb['description']) ?></p>
        </div>

        <!-- Chuỗi streak -->
        <div class="streak text-orange-400 font-semibold ml-auto">
            <?= $hb['current_streak'] ?> ngày
        </div>

    </div>
<?php endforeach; ?>
</div>
</section>

<!-- POPUP THÊM THÓI QUEN -->
<div id="addHabitPopup" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden transition-opacity duration-200">
  <div class="bg-white rounded-2xl shadow-2xl p-6 w-96 relative animate-fadeIn">

    <!-- Nút X -->
    <button id="closePopup"
            class="absolute top-3 right-3 text-gray-500 hover:text-red-500 text-xl">
      ✕
    </button>

    <h2 class="text-2xl font-bold mb-4 text-center text-purple-700">
      Thêm Thói Quen Mới ✨
    </h2>

    <form id="addHabitForm" class="space-y-4">

      <div>
        <label class="block text-gray-700 font-medium mb-1">Tên thói quen</label>
        <input type="text" name="habit_name" required
               class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-purple-400 focus:outline-none">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Mô tả</label>
        <textarea name="description"
                  class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-purple-400 focus:outline-none"></textarea>
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-1">Icon</label>
        <input type="text" name="icon" placeholder="Ví dụ: 🌞 hoặc drink.png"
               class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-purple-400 focus:outline-none">
      </div>

      <!-- NÚT LƯU THÓI QUEN -->
      <button type="submit"
              class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl text-lg font-semibold shadow-md transition">
        💾 Lưu Thói Quen
      </button>

    </form>
  </div>
</div>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to   { opacity: 1; transform: scale(1); }
}
.animate-fadeIn { animation: fadeIn 0.25s ease-out; }
</style>

<script src="./assets/js/dashboard.js"></script>
</body>
</html>
