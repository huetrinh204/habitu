<?php
session_start();
include "../config.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location: dangnhap.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// Lấy quyền user
$stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$role = $stmt->fetchColumn();

// Nếu không phải admin → không cho truy cập
if ($role !== "admin") {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cài Đặt - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-tr from-cyan-300 to-sky-400 min-h-screen">

    <?php include "navbar.php"; ?>

    <div class="px-4 md:px-10 py-5">
        <h1 class="text-2xl md:text-3xl font-bold text-white" style="text-shadow: 2px 2px 6px rgba(0,0,0,0.5);">
            Cài Đặt Hệ Thống
        </h1>
        <p class="text-gray-700 mb-6 text-sm md:text-base">
            Quản lý cấu hình, thông tin ứng dụng và quyền truy cập
        </p>

        <!-- Section 1: Thông tin Admin -->
        <div class="bg-white shadow rounded-lg p-4 md:p-5 mb-6">
            <h2 class="text-lg md:text-xl font-semibold mb-3">Thông tin Admin</h2>

            <!-- Responsive grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-1">Tên hiển thị</label>
                    <input type="text" class="w-full border border-gray-300 px-3 py-2 rounded" placeholder="Admin">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Email</label>
                    <input type="email" class="w-full border border-gray-300 px-3 py-2 rounded"
                        placeholder="admin@example.com">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Mật khẩu mới</label>
                    <input type="password" class="w-full border border-gray-300 px-3 py-2 rounded" placeholder="••••••">
                </div>

                <div class="flex items-end">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow w-full md:w-auto">
                        Cập nhật
                    </button>
                </div>
            </div>
        </div>

        <!-- Section 2: Cấu hình hệ thống -->
        <div class="bg-white shadow rounded-lg p-4 md:p-5 mb-6">
            <h2 class="text-lg md:text-xl font-semibold mb-3">Cấu hình hệ thống</h2>

            <div class="flex flex-col gap-4 text-sm md:text-base">

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="maintenance" class="h-5 w-5">
                    <label for="maintenance" class="text-gray-700">Bật chế độ bảo trì</label>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="email_notify" class="h-5 w-5">
                    <label for="email_notify" class="text-gray-700">Gửi thông báo email cho người dùng</label>
                </div>

                <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow w-full md:w-48">
                    Lưu cài đặt
                </button>
            </div>
        </div>

        <!-- Section 3: Thông tin Website -->
        <div class="bg-white shadow rounded-lg p-4 md:p-5">
            <h2 class="text-lg md:text-xl font-semibold mb-3">Thông tin Website</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-gray-700 mb-1">Tên website</label>
                    <input type="text" class="w-full border border-gray-300 px-3 py-2 rounded" value="Habitu">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Mô tả</label>
                    <input type="text" class="w-full border border-gray-300 px-3 py-2 rounded" value="Habitu">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Phiên bản</label>
                    <input type="text" class="w-full border border-gray-300 px-3 py-2 rounded" value="1.0.0">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Liên hệ hỗ trợ</label>
                    <input type="text" class="w-full border border-gray-300 px-3 py-2 rounded"
                        value="support@habitu.com">
                </div>
            </div>
        </div>
    </div>

</body>

</html>