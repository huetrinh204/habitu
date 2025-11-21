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
<body class="bg-gradient-to-br from-cyan-300 via-teal-300 to-teal-500 min-h-screen">

<?php include "navbar.php"; ?>


<!-- PAGE TITLE -->
<header class="text-center py-6">
  <h2 class="text-2xl font-semibold text-white drop-shadow-lg">
    Tài Khoản Cá Nhân 🐱✨
  </h2>
  <p class="text-gray-100">Cập nhật thông tin và bảo mật tài khoản của bạn</p>
</header>


<!-- MAIN CONTENT -->
<div class="max-w-4xl mx-auto px-6 space-y-6 pb-16">

    <!-- Hồ sơ cá nhân -->
    <div class="bg-white/80 backdrop-blur-md shadow-lg rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 flex gap-2">
            <i class="fas fa-id-card text-blue-500"></i> Hồ Sơ Cá Nhân
        </h3>

        <label class="text-sm font-medium">Tên hiển thị</label>
        <input type="text" class="w-full mt-1 mb-4 p-2 border rounded-lg" placeholder="Người dùng">

        <label class="text-sm font-medium">Email</label>
        <input type="email" class="w-full mt-1 mb-4 p-2 border rounded-lg" placeholder="user@example.com">

        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
            Cập Nhật Thông Tin
        </button>
    </div>


    <!-- Đổi mật khẩu -->
    <div class="bg-white/80 backdrop-blur-md shadow-lg rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 flex gap-2">
            <i class="fas fa-lock text-orange-500"></i> Đổi Mật Khẩu
        </h3>

        <label class="text-sm font-medium">Mật khẩu hiện tại</label>
        <input type="password" class="w-full mt-1 mb-3 p-2 border rounded-lg">

        <label class="text-sm font-medium">Mật khẩu mới</label>
        <input type="password" class="w-full mt-1 mb-3 p-2 border rounded-lg">

        <label class="text-sm font-medium">Xác nhận mật khẩu mới</label>
        <input type="password" class="w-full mt-1 mb-4 p-2 border rounded-lg">

        <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg">
            Đổi Mật Khẩu
        </button>
    </div>


    <!-- Thông báo -->
    <div class="bg-white/80 backdrop-blur-md shadow-lg rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 flex gap-2">
            <i class="fas fa-bell text-purple-500"></i> Thông Báo
        </h3>

        <!-- notification -->
        <div class="flex justify-between py-3 border-b">
            <span>Thông báo đẩy</span>
            <label class="switch">
                <input type="checkbox">
                <span class="slider"></span>
            </label>
        </div>

        <div class="flex justify-between py-3">
            <span>Thông báo email</span>
            <label class="switch">
                <input type="checkbox">
                <span class="slider"></span>
            </label>
        </div>
    </div>

</div>


<!-- CUSTOM SWITCH STYLE -->
<style>
.switch {
  position: relative;
  width: 46px;
  height: 24px;
  display: inline-block;
}
.switch input { display: none; }
.slider {
  position: absolute;
  cursor: pointer;
  background-color: #ccc;
  border-radius: 34px;
  inset: 0;
  transition: .4s;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  border-radius: 50%;
  transition: .4s;
}
input:checked + .slider {
  background-color: #4ade80;
}
input:checked + .slider:before {
  transform: translateX(22px);
}
</style>

</body>
</html>
