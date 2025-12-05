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
    
<!-- NAV -->
<?php include "navbar.php"; ?>


<!-- HEADER -->
  <div class="mb-6 text-center mt-10">

    <h1 class="text-2xl font-bold text-white leading-loose">
        Trung Tâm Hỗ Trợ 🐱
    </h1>

    <p class="text-sm text-white leading-relaxed">
        Chúng mình luôn sẵn sàng giúp bạn sử dụng Habitu tốt hơn! ✨
    </p>
</div>

<!-- MAIN -->
<section class="max-w-5xl mx-auto px-6 pb-20 space-y-8">

    <!-- HƯỚNG DẪN NHANH -->
    <div class="bg-white/90 p-6 rounded-3xl shadow-lg">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
          <i class="fa-solid fa-lightbulb text-yellow-500"></i> Hướng dẫn nhanh
        </h3>

        <div class="grid grid-cols-3 gap-4">
            <div class="p-4 bg-purple-100 rounded-xl">
                <h4 class="font-semibold">➕ Tạo thói quen</h4>
                <p class="text-sm text-gray-600">Vào Trang Chủ → nhấn “Thêm Thói Quen”.</p>
            </div>

            <div class="p-4 bg-teal-100 rounded-xl">
                <h4 class="font-semibold">📝 Ghi nhật ký</h4>
                <p class="text-sm text-gray-600">Vào mục Nhật Ký để lưu lại cảm nghĩ mỗi ngày.</p>
            </div>

            <div class="p-4 bg-blue-100 rounded-xl">
                <h4 class="font-semibold">📊 Xem thống kê</h4>
                <p class="text-sm text-gray-600">Theo dõi tiến trình ở trang Thống Kê.</p>
            </div>
        </div>
    </div>


    <!-- FAQ -->
    <div class="bg-white/90 p-6 rounded-3xl shadow-lg">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
          <i class="fa-solid fa-circle-question text-blue-500"></i> Câu hỏi thường gặp (FAQ)
        </h3>

        <div class="space-y-4">

            <details class="bg-gray-100 p-4 rounded-xl cursor-pointer">
                <summary class="font-semibold">Làm sao để đặt lại mật khẩu?</summary>
                <p class="text-gray-600 mt-2">Bạn có thể đổi mật khẩu trong mục Tài Khoản → Đổi mật khẩu.</p>
            </details>

            <details class="bg-gray-100 p-4 rounded-xl cursor-pointer">
                <summary class="font-semibold">Tôi muốn xóa thói quen?</summary>
                <p class="text-gray-600 mt-2">Trong Trang Chủ, nhấn vào thói quen → chọn Xóa.</p>
            </details>

            <details class="bg-gray-100 p-4 rounded-xl cursor-pointer">
                <summary class="font-semibold">Làm sao để tạo thói quen mới?</summary>
                <p class="text-gray-600 mt-2">Để tạo thói quen mới, bạn chỉ cần vào mục “Thói quen” và chọn nút “+ Thêm”.  
Tại đây bạn có thể đặt tên, mô tả, tần suất và thời gian nhắc nhở theo ý muốn.</p>
            </details>

        </div>
    </div>


    <!-- FORM LIÊN HỆ -->
    <div class="bg-white/90 p-6 rounded-3xl shadow-lg">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-envelope text-red-500"></i> Liên hệ hỗ trợ
        </h3>

        <form class="space-y-4">

            <div>
                <label class="text-sm font-medium">Tên của bạn</label>
                <input type="text" class="w-full p-2 border rounded-lg mt-1" placeholder="Tên người dùng">
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input type="email" class="w-full p-2 border rounded-lg mt-1" placeholder="you@example.com">
            </div>

            <div>
                <label class="text-sm font-medium">Nội dung</label>
                <textarea class="w-full p-2 border rounded-lg mt-1 h-28" placeholder="Bạn đang gặp vấn đề gì?"></textarea>
            </div>

            <button class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                Gửi yêu cầu hỗ trợ
            </button>

        </form>
    </div>

</section>


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
