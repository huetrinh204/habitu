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


<!-- HEADER -->
<header class="text-center py-6">
  <h2 class="text-2xl font-semibold text-white drop-shadow-lg">Trung Tâm Hỗ Trợ 🐱✨</h2>
  <p class="text-gray-100">Chúng mình luôn sẵn sàng giúp bạn sử dụng Habitu tốt hơn!</p>
</header>


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

</body>
</html>
