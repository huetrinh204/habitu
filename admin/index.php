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

if ($role !== "admin") {
    header("Location: ../index.php");
    exit();
}

/* ================== THỐNG KÊ ================== */
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalHabits = $pdo->query("SELECT COUNT(*) FROM habit")->fetchColumn();
$totalPosts = $pdo->query("SELECT COUNT(*) FROM post")->fetchColumn();
$totalComments = $pdo->query("SELECT COUNT(*) FROM comment")->fetchColumn();

/* ================== USERS PER DAY ================== */
$q1 = $pdo->query("SELECT DATE(create_acc) AS day, COUNT(*) AS total FROM users GROUP BY day ORDER BY day ASC");
$userDays = [];
$userCounts = [];
while ($row = $q1->fetch(PDO::FETCH_ASSOC)) {
    $userDays[] = $row["day"];
    $userCounts[] = $row["total"];
}

/* ================== POSTS PER DAY ================== */
$q2 = $pdo->query("SELECT DATE(created_at) AS day, COUNT(*) AS total FROM post GROUP BY day ORDER BY day ASC");
$postDays = [];
$postCounts = [];
while ($row = $q2->fetch(PDO::FETCH_ASSOC)) {
    $postDays[] = $row["day"];
    $postCounts[] = $row["total"];
}

/* ================== COMMENTS PER DAY ================== */
$q4 = $pdo->query("SELECT DATE(created_cmt) AS day, COUNT(*) AS total FROM comment GROUP BY day ORDER BY day ASC");
$commentDays = [];
$commentCounts = [];
while ($row = $q4->fetch(PDO::FETCH_ASSOC)) {
    $commentDays[] = $row["day"];
    $commentCounts[] = $row["total"];
}

/* ================== DANH SÁCH USER ================== */
$users = $pdo->query("SELECT user_id, username FROM users ORDER BY username ASC")->fetchAll(PDO::FETCH_ASSOC);

/* ================== USER MẶC ĐỊNH ================== */
$q = $pdo->query("SELECT user_id FROM habit ORDER BY created_hb DESC LIMIT 1");
$defaultUser = $q->fetchColumn();
if (!$defaultUser)
    $defaultUser = $users[0]["user_id"];

/* ================== API TRẢ DỮ LIỆU THEO USER TUẦN ================== */
if (isset($_GET["loadHabitUser"])) {
    $uid = $_GET["loadHabitUser"];
    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
    $endOfWeek   = date('Y-m-d', strtotime('sunday this week'));

    $stm = $pdo->prepare("
        SELECT DATE(created_hb) AS day, COUNT(*) AS total
        FROM habit
        WHERE user_id = ? AND DATE(created_hb) BETWEEN ? AND ?
        GROUP BY day
        ORDER BY day ASC
    ");
    $stm->execute([$uid, $startOfWeek, $endOfWeek]);
    $rows = $stm->fetchAll(PDO::FETCH_ASSOC);

    // Tạo mảng 7 ngày tuần hiện tại
    $weekDays = [];
    $habitCounts = [];
    $current = strtotime($startOfWeek);
    $end = strtotime($endOfWeek);
    $dataMap = [];
    foreach ($rows as $r) {
        $dataMap[$r['day']] = $r['total'];
    }
    while ($current <= $end) {
        $dateStr = date('Y-m-d', $current);
        $weekDays[] = date('d/m/Y', $current);
        $habitCounts[] = $dataMap[$dateStr] ?? 0;
        $current = strtotime('+1 day', $current);
    }

    echo json_encode([
        "weekDays" => $weekDays,
        "habitCounts" => $habitCounts
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Habitu Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .habit-box:hover { transform: translateY(-4px); }
    </style>
</head>
<body class="min-h-screen" style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0)">
    <?php include "navbar.php"; ?>

    <div class="px-6 md:px-10 py-5">
        <h1 class="text-3xl font-bold text-white drop-shadow-lg">Dashboard</h1>
        <p class="text-gray-700 mb-6 text-sm sm:text-base">Quản lý tổng quan hoạt động người dùng</p>
    </div>

    <!-- METRICS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 px-6 md:px-10">
        <div class="p-6 bg-white shadow rounded-2xl text-center habit-box transition">
            <i class="fas fa-users text-3xl text-purple-600 mb-2"></i>
            <h3 class="text-xl font-bold"><?= $totalUsers ?></h3>
            <p class="text-gray-600">Tổng người dùng</p>
        </div>
        <div class="p-6 bg-white shadow rounded-2xl text-center habit-box transition">
            <i class="fas fa-pen text-3xl text-pink-500 mb-2"></i>
            <h3 class="text-xl font-bold"><?= $totalPosts ?></h3>
            <p class="text-gray-600">Tổng bài viết</p>
        </div>
        <div class="p-6 bg-white shadow rounded-2xl text-center habit-box transition">
            <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
            <h3 class="text-xl font-bold"><?= $totalHabits ?></h3>
            <p class="text-gray-600">Tổng thói quen</p>
        </div>
        <div class="p-6 bg-white shadow rounded-2xl text-center habit-box transition">
            <i class="fas fa-comment text-3xl text-blue-500 mb-2"></i>
            <h3 class="text-xl font-bold"><?= $totalComments ?></h3>
            <p class="text-gray-600">Tổng bình luận</p>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-6 md:px-10 mt-12 mb-16">

        <!-- Người dùng -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-bold mb-3 text-purple-700">Tăng trưởng người dùng theo ngày</h3>
            <canvas id="userChart" height="120"></canvas>
        </div>

        <!-- Bài viết -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-bold mb-3 text-pink-600">Số bài viết theo ngày</h3>
            <canvas id="postChart" height="120"></canvas>
        </div>

        <!-- Thói quen tuần -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <div class="flex items-center justify-between mb-3">
                <h3 id="habitChartTitle" class="text-lg font-bold text-green-600">
                    Số thói quen tuần này của <?= htmlspecialchars($users[array_search($defaultUser, array_column($users,'user_id'))]['username']) ?>
                </h3>
                <select id="habitUserSelect" class="border px-3 py-1 rounded text-sm" onchange="changeUserHabits()">
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['user_id'] ?>" <?= $u["user_id"]==$defaultUser?'selected':'' ?>>
                            <?= htmlspecialchars($u['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <canvas id="habitChartUser" height="120"></canvas>
        </div>

        <!-- Bình luận -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-bold mb-3 text-red-600">Số bình luận theo ngày</h3>
            <canvas id="commentChart" height="120"></canvas>
        </div>

    </div>

    <script>
        // Chart người dùng
        new Chart(document.getElementById("userChart"), {
            type: 'line',
            data: { labels: <?= json_encode($userDays) ?>, datasets: [{ label: 'Người dùng mới', data: <?= json_encode($userCounts) ?>, borderWidth: 2, tension: 0.3 }] }
        });

        // Chart bài viết
        new Chart(document.getElementById("postChart"), {
            type: 'bar',
            data: {
                labels: <?= json_encode($postDays) ?>,
                datasets: [{ label: 'Bài viết', data: <?= json_encode($postCounts) ?>, backgroundColor: 'pink', borderWidth: 2 }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Chart bình luận
        new Chart(document.getElementById("commentChart"), {
            type: 'bar',
            data: { labels: <?= json_encode($commentDays) ?>, datasets: [{ label: 'Bình luận', data: <?= json_encode($commentCounts) ?>, borderWidth: 2 }] }
        });

        // Chart thói quen theo tuần (line chart, y = số nguyên)
        let habitChartInstance = null;
        function changeUserHabits() {
            let uid = document.getElementById("habitUserSelect").value;
            let selectedUser = document.querySelector("#habitUserSelect option:checked").text;
            document.getElementById("habitChartTitle").textContent = "Số thói quen tuần này của " + selectedUser;

            fetch("?loadHabitUser=" + uid)
                .then(res => res.json())
                .then(data => {
                    const ctx = document.getElementById("habitChartUser").getContext("2d");
                    if (habitChartInstance) habitChartInstance.destroy();

                    habitChartInstance = new Chart(ctx, {
                        type: "line",
                        data: {
                            labels: data.weekDays,
                            datasets: [{
                                label: "Số thói quen",
                                data: data.habitCounts,
                                borderColor: "green",
                                backgroundColor: "rgba(0,128,0,0.2)",
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: { title: { display: true, text: "Ngày trong tuần" } },
                                y: {
                                    title: { display: true, text: "Số thói quen" },
                                    beginAtZero: true,
                                    ticks: { stepSize: 1 } // <--- bắt buộc trục y hiển thị số nguyên
                                }
                            }
                        }
                    });
                });
        }

        window.onload = changeUserHabits;
    </script>

</body>
</html>