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
<body style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0)";>

<?php include "navbar.php"; ?>


<!-- PAGE TITLE -->
 <!-- Header -->
  
 <div class="mb-6 text-center mt-10">

    <h1 class="text-2xl font-bold text-white leading-loose">
        Tài Khoản Cá Nhân 🐱
    </h1>

    <p class="text-sm text-white leading-relaxed">
        Cập nhật thông tin và bảo mật tài khoản của bạn ✨ 
    </p>
</div>

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


<!-- FOOTER -->
<footer class="mt-10 bg-gradient-to-r from-purple-600 to-pink-500 text-white py-10 px-8 rounded-t-3xl">

  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10">

    <!-- Logo + mô tả -->
    <div>
      <div class="flex items-center gap-3 mb-3">
        <img src="assets/logo_habitu.png" width="40" class="rounded-full" />
        <h2 class="text-xl font-bold">Habitu</h2>
      </div>
      <p class="text-sm leading-relaxed">
        Xây dựng thói quen lành mạnh cùng Habitu! 🐱✨
      </p>

      <!-- Social icons -->
      <div class="flex gap-4 mt-4 text-xl">
        <a href="#" class="hover:text-yellow-300"><i class="fab fa-facebook"></i></a>
        <a href="#" class="hover:text-yellow-300"><i class="fab fa-twitter"></i></a>
        <a href="#" class="hover:text-yellow-300"><i class="fab fa-instagram"></i></a>
        <a href="#" class="hover:text-yellow-300"><i class="fab fa-youtube"></i></a>
      </div>
    </div>

    <!-- Liên kết nhanh -->
    <div>
      <h3 class="text-lg font-semibold mb-3">Liên Kết Nhanh</h3>
      <ul class="space-y-2 text-sm">
        <li><a href="dashboard.php" class="hover:text-yellow-300">Trang Chủ</a></li>
        <li><a href="journal.php" class="hover:text-yellow-300">Nhật Ký</a></li>
        <li><a href="community.php" class="hover:text-yellow-300">Cộng Đồng</a></li>
        <li><a href="thongke.php" class="hover:text-yellow-300">Thống Kê</a></li>
      </ul>
    </div>

    <!-- Tài nguyên -->
    <div>
      <h3 class="text-lg font-semibold mb-3">Tài Nguyên</h3>
      <ul class="space-y-2 text-sm">
        <li><a href="index.php" class="hover:text-yellow-300">Hướng Dẫn Sử Dụng</a></li>
        <li><a href="#" class="hover:text-yellow-300">Blog</a></li>
        <li><a href="#" class="hover:text-yellow-300">Câu Hỏi Thường Gặp</a></li>
        <li><a href="support.php" class="hover:text-yellow-300">Hỗ Trợ</a></li>
      </ul>
    </div>

    <!-- Liên hệ -->
    <div>
      <h3 class="text-lg font-semibold mb-3">Liên Hệ</h3>

      <p class="text-sm flex items-center gap-2">
        <i class="fas fa-envelope"></i> support@habitu.com
      </p>

      <p class="text-sm mt-3">Giờ làm việc:</p>
      <p class="text-sm">T2 - T6: 9:00 - 18:00</p>
    </div>

  </div>

  <!-- Dòng cuối -->
  <div class="text-center text-xs mt-10 opacity-80">
    © 2025 Habitu. Tất cả quyền được bảo lưu. |
    <a href="#" class="hover:text-yellow-300">Chính Sách Bảo Mật</a> • 
    <a href="#" class="hover:text-yellow-300">Điều Khoản Sử Dụng</a>
    <br>
    <div class="mt-2 flex justify-center items-center gap-1">
      Made with ❤️ by TMeo
    </div>
  </div>

</footer>
</body>
</html>
