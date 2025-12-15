<?php
session_start();
include "config.php"; // Kết nối cơ sở dữ liệu

// Nếu chưa đăng nhập → chuyển về trang đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location: dangnhap.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// ===============================
// LẤY FEEDBACK CỦA USER + PHẢN HỒI ADMIN
// ===============================
$stmt = $pdo->prepare("
    SELECT message, reply_message, status_fb, created_fb, replied_at
    FROM feedbacks
    WHERE user_id = ?
    ORDER BY created_fb DESC
");
$stmt->execute([$user_id]);
$userFeedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý form gửi feedback
if (isset($_POST['submit_feedback'])) {
    $message = trim($_POST['message']);

    if ($message !== "") {
        $stmt = $pdo->prepare("
            INSERT INTO feedbacks (message, status_fb, created_fb, user_id)
            VALUES (?, 'unread', NOW(), ?)
        ");
        $stmt->execute([$message, $user_id]);
        $_SESSION['success'] = "Gửi yêu cầu hỗ trợ thành công 🎉";
    } else {
        $_SESSION['error'] = "Vui lòng nhập nội dung trước khi gửi ❌";
    }

    header("Location: support.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trung Tâm Hỗ Trợ | Habitu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0);">

    <?php include "navbar.php"; ?>

    <!-- HEADER -->
    <div class="mb-6 text-center mt-10">
        <h1 class="text-2xl font-bold text-white leading-loose">Trung Tâm Hỗ Trợ 🐱</h1>
        <p class="text-sm text-white leading-relaxed">Chúng mình luôn sẵn sàng giúp bạn sử dụng Habitu tốt hơn! ✨</p>
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
                    <p class="text-gray-600 mt-2">Trong mục Thói Quen, nhấn vào thói quen → chọn biểu tượng 🗑️ .</p>
                </details>
                <details class="bg-gray-100 p-4 rounded-xl cursor-pointer">
                    <summary class="font-semibold">Làm sao để tạo thói quen mới?</summary>
                    <p class="text-gray-600 mt-2">Vào mục “Thói quen” và chọn nút “+ Thêm”. Đặt tên, mô tả, tần suất và
                        nhắc nhở theo ý muốn.</p>
                </details>
            </div>
        </div>

<!-- PHẢN HỒI CỦA BẠN -->
<div class="bg-white/90 p-6 rounded-3xl shadow-lg">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <i class="fa-solid fa-comments text-green-500"></i>
        Phản hồi của bạn
    </h3>

    <?php if (empty($userFeedbacks)): ?>
        <p class="text-gray-500 text-sm">
            Bạn chưa gửi yêu cầu hỗ trợ nào.
        </p>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($userFeedbacks as $fb): ?>
                <div class="border rounded-xl p-4 bg-gray-50">
                    <!-- USER MESSAGE -->
                    <p class="text-sm text-gray-500 mb-1">
                        <i class="fa-solid fa-paper-plane"></i>
                        Gửi lúc <?= date("d/m/Y H:i", strtotime($fb["created_fb"])) ?>
                    </p>

                    <p class="text-gray-800">
                        <?= nl2br(htmlspecialchars($fb["message"])) ?>
                    </p>

                    <!-- ADMIN REPLY -->
                    <?php if (!empty($fb["reply_message"])): ?>
                        <div class="mt-3 bg-green-100 border border-green-200 rounded-lg p-3">
                            <p class="text-sm font-semibold text-green-700">
                                <i class="fa-solid fa-reply"></i> Phản hồi từ Admin
                            </p>
                            <p class="text-gray-700 mt-1">
                                <?= nl2br(htmlspecialchars($fb["reply_message"])) ?>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Trả lời lúc <?= date("d/m/Y H:i", strtotime($fb["replied_at"])) ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <p class="mt-3 text-sm text-yellow-600">
                            <i class="fa-solid fa-hourglass-half"></i>
                            Yêu cầu đang được xử lý...
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

        <!-- FORM LIÊN HỆ -->
        <div class="bg-white/90 p-6 rounded-3xl shadow-lg">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-envelope text-red-500"></i> Liên hệ hỗ trợ
            </h3>

            <form method="post" class="space-y-4">
                <div>
                    <label class="text-sm font-medium">Tên của bạn</label>
                    <input type="text" class="w-full p-2 border rounded-lg mt-1"
                        value="<?= htmlspecialchars($username) ?>" readonly>
                </div>

                <div>
                    <label class="text-sm font-medium">Nội dung</label>
                    <textarea name="message" class="w-full p-2 border rounded-lg mt-1 h-28"
                        placeholder="Bạn đang gặp vấn đề gì?"></textarea>
                </div>

                <button type="submit" name="submit_feedback"
                    class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                    Gửi yêu cầu hỗ trợ
                </button>
            </form>
        </div>

    </section>

    <?php include "footer.php"; ?>

    <!-- ===================== POP-UP Ở GIỮA MÀN HÌNH ===================== -->
    <script>
        function showPopup(message, type = "success") {
            const overlay = document.createElement("div");
            overlay.style.position = "fixed";
            overlay.style.top = "0";
            overlay.style.left = "0";
            overlay.style.width = "100vw";
            overlay.style.height = "100vh";
            overlay.style.backgroundColor = "rgba(0,0,0,0.3)";
            overlay.style.display = "flex";
            overlay.style.alignItems = "center";
            overlay.style.justifyContent = "center";
            overlay.style.zIndex = "9999";

            const popup = document.createElement("div");
            popup.textContent = message;
            popup.className = `px-6 py-4 rounded-lg shadow-lg text-white text-center text-lg transition-all duration-500
                       ${type === "success" ? "bg-green-500" : "bg-red-500"}`;

            overlay.appendChild(popup);
            document.body.appendChild(overlay);

            setTimeout(() => {
                overlay.style.opacity = '0';
                overlay.style.transition = "opacity 0.5s";
                setTimeout(() => overlay.remove(), 500);
            }, 3000);
        }

        // Hiển thị thông báo nếu có
        <?php if (isset($_SESSION["success"])): ?>
            showPopup("<?= $_SESSION['success'] ?>", "success");
            <?php unset($_SESSION["success"]); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION["error"])): ?>
            showPopup("<?= $_SESSION['error'] ?>", "error");
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>
    </script>

</body>

</html>