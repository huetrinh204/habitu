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
// Kiểm tra quyền admin
$stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$role = $stmt->fetchColumn();

if ($role !== "admin") {
    header("Location: ../index.php");
    exit();
}

/* =============================
   XỬ LÝ ADMIN TRẢ LỜI FEEDBACK
============================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reply_feedback"])) {
    $feedback_id = $_POST["feedback_id"];
    $reply_message = trim($_POST["reply_message"]);

    if (!empty($reply_message)) {
        $stmt = $pdo->prepare("
            UPDATE feedbacks 
            SET reply_message = ?, 
                replied_at = NOW(),
                status_fb = 'read'
            WHERE feedback_id = ?
        ");
        $stmt->execute([$reply_message, $feedback_id]);
    }
}

/* =============================
   LẤY DỮ LIỆU FEEDBACK
============================= */
$feedbacks = $pdo->query("
    SELECT f.*, u.username, u.email
    FROM feedbacks f
    JOIN users u ON f.user_id = u.user_id
    ORDER BY f.created_fb DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* =============================
   THỐNG KÊ
============================= */
$total = $pdo->query("SELECT COUNT(*) FROM feedbacks")->fetchColumn();
$new = $pdo->query("SELECT COUNT(*) FROM feedbacks WHERE status_fb = 'unread'")->fetchColumn();
$unprocessed = $pdo->query("SELECT COUNT(*) FROM feedbacks WHERE status_fb = 'unread'")->fetchColumn();
$done = $pdo->query("SELECT COUNT(*) FROM feedbacks WHERE status_fb = 'read'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản Lý Phản Hồi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-tr from-cyan-300 to-sky-400 min-h-screen">

    <?php include "navbar.php"; ?>

    <div class="px-6 py-6">

        <!-- HEADER -->
        <div class="bg-cyan-50 rounded-xl p-6 mb-6">
            <h1 class="text-xl font-bold text-purple-600">Quản Lý Phản Hồi</h1>
            <p class="text-sm text-gray-500">Xử lý phản hồi và hỗ trợ người dùng</p>

            <!-- STATS -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5">
                <?php
                $cards = [
                    ["Tổng số", $total, "text-blue-500", "ri-message-3-line"],
                    ["Mới", $new, "text-indigo-500", "ri-notification-3-line"],
                    ["Chưa xử lý", $unprocessed, "text-orange-500", "ri-time-line"],
                    ["Đã giải quyết", $done, "text-green-500", "ri-checkbox-circle-line"]
                ];
                foreach ($cards as $c):
                    ?>
                    <div class="bg-white rounded-xl shadow p-4 flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500"><?= $c[0] ?></p>
                            <p class="text-xl font-bold"><?= $c[1] ?></p>
                        </div>
                        <i class="<?= $c[3] ?> text-3xl <?= $c[2] ?>"></i>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- LIST FEEDBACK -->
        <div class="space-y-4">
            <?php foreach ($feedbacks as $fb): ?>
                <div class="bg-white rounded-xl shadow p-5">

                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-gray-800">
                                Phản hồi từ <?= htmlspecialchars($fb["username"]) ?>
                            </h3>
                            <p class="text-sm text-gray-500 flex items-center gap-3">
                                <span><i class="ri-mail-line"></i> <?= $fb["email"] ?></span>
                                <span><i class="ri-time-line"></i>
                                    <?= date("d/m/Y H:i", strtotime($fb["created_fb"])) ?></span>
                            </p>
                        </div>

                        <span class="px-3 py-1 rounded-full text-xs
                    <?= $fb["status_fb"] === "unread" ? "bg-blue-100 text-blue-600" : "" ?>
                    
                    <?= $fb["status_fb"] === "read" ? "bg-green-100 text-green-600" : "" ?>">
                            <?php
                            if ($fb["status_fb"] === "unread")
                                echo "Chưa xử lý";
                            elseif ($fb["status_fb"] === "read")
                                echo "Đã giải quyết";
                            ?>
                        </span>
                    </div>

                    <!-- MESSAGE -->
                    <p class="mt-3 text-gray-700">
                        <?= nl2br(htmlspecialchars($fb["message"])) ?>
                    </p>

                    <!-- ADMIN REPLY -->
                    <?php if (!empty($fb["reply_message"])): ?>
                        <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-green-700 font-semibold">
                                <i class="ri-reply-line"></i> Phản hồi từ Admin
                            </p>
                            <p class="text-gray-700 mt-1">
                                <?= nl2br(htmlspecialchars($fb["reply_message"])) ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <form method="post" class="mt-4">
                            <input type="hidden" name="feedback_id" value="<?= $fb["feedback_id"] ?>">
                            <textarea name="reply_message" class="w-full border rounded-lg p-3 text-sm" rows="3"
                                placeholder="Nhập phản hồi cho người dùng..."></textarea>
                            <button type="submit" name="reply_feedback"
                                class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                                Gửi phản hồi
                            </button>
                        </form>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>

</html>