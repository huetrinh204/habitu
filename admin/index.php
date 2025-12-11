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

/* ================== HABITS PER DAY ================== */
$q3 = $pdo->query("SELECT DATE(created_hb) AS day, COUNT(*) AS total FROM habit GROUP BY day ORDER BY day ASC");
$habitDays = [];
$habitCounts = [];
while ($row = $q3->fetch(PDO::FETCH_ASSOC)) {
    $habitDays[] = $row["day"];
    $habitCounts[] = $row["total"];
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

/* ================== USER MỚI TẠO THÓI QUEN GẦN NHẤT ================== */
$q = $pdo->query("SELECT user_id FROM habit ORDER BY created_hb DESC LIMIT 1");
$defaultUser = $q->fetchColumn();
if (!$defaultUser)
    $defaultUser = $users[0]["user_id"]; // fallback nếu chưa ai tạo thói quen
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Habitu Admin Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .habit-box:hover {
            transform: translateY(-4px);
        }
    </style>
</head>

<body class="min-h-screen" style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0)">

    <!-- NAVBAR -->
    <?php include "navbar.php"; ?>

    <div class="px-6 md:px-10 py-5">
        <h1 class="text-3xl font-bold text-white drop-shadow-lg">Dashboard</h1>
        <p class="text-gray-700 mb-6 text-sm sm:text-base">Quản lý tổng quan hoạt động người dùng</p>
    </div>

    <!-- ===================== METRICS ===================== -->
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

    <!-- ===================== CHARTS ===================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-6 md:px-10 mt-12 mb-16">

        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-bold mb-3 text-purple-700">Tăng trưởng người dùng theo ngày</h3>
            <canvas id="userChart" height="120"></canvas>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-bold mb-3 text-pink-600">Số bài viết theo ngày</h3>
            <canvas id="postChart" height="120"></canvas>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
            <div class="flex items-center justify-between mb-3">
                <h3 id="habitChartTitle" class="text-lg font-bold text-green-600">
                    Số thói quen theo ngày
                    (<?= htmlspecialchars($users[array_search($defaultUser, array_column($users, 'user_id'))]['username']) ?>)
                </h3>

                <!-- DROPDOWN CHỌN USER -->
                <select id="habitUserSelect" class="border px-3 py-1 rounded text-sm" onchange="changeUserHabits()">

                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['user_id'] ?>" <?= $u["user_id"] == $defaultUser ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['username']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>
            <!-- Biểu đồ thói quen toàn hệ thống -->
            <canvas id="habitChartDefault" height="120" class="mb-8"></canvas>

            <!-- Biểu đồ thói quen theo user -->
            <canvas id="habitChartUser" height="120"></canvas>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
            <h3 class="text-lg font-bold mb-3 text-red-600">Số bình luận theo ngày</h3>
            <canvas id="commentChart" height="120"></canvas>
        </div>

    </div>

    <?php
    /* API trả về dữ liệu thói quen theo user */
    if (isset($_GET["loadHabitUser"])) {
        $uid = $_GET["loadHabitUser"];

        $stm = $pdo->prepare("
        SELECT DATE(created_hb) AS day, COUNT(*) AS total
        FROM habit
        WHERE user_id = ?
        GROUP BY day
        ORDER BY day ASC
    ");
        $stm->execute([$uid]);
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "days" => array_column($rows, "day"),
            "counts" => array_column($rows, "total")
        ]);
        exit;
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        /* CHARTS */
        new Chart(userChart, {
            type: 'line',
            data: {
                labels: <?= json_encode($userDays) ?>,
                datasets: [{ label: 'Người dùng mới', data: <?= json_encode($userCounts) ?>, borderWidth: 2, tension: 0.3 }]
            }
        });

        new Chart(postChart, {
            type: 'bar',
            data: {
                labels: <?= json_encode($postDays) ?>,
                datasets: [{ label: 'Bài viết', data: <?= json_encode($postCounts) ?>, borderWidth: 2 }]
            }
        });

        new Chart(document.getElementById("habitChartDefault"), {
            type: 'line',
            data: {
                labels: <?= json_encode($habitDays) ?>,
                datasets: [{ label: 'Thói quen mới', data: <?= json_encode($habitCounts) ?>, borderWidth: 2, tension: 0.3 }]
            }
        });

        new Chart(commentChart, {
            type: 'bar',
            data: {
                labels: <?= json_encode($commentDays) ?>,
                datasets: [{ label: 'Bình luận', data: <?= json_encode($commentCounts) ?>, borderWidth: 2 }]
            }
        });


        //CHỌN NGƯỜI DÙNG ĐỂ XEM THÓI QUEN NGƯỜI DÙNG ĐÃ TẠO 
        let habitChartInstance = null;

        function changeUserHabits() {
            let uid = document.getElementById("habitUserSelect").value;
            let selectedUser = document.querySelector("#habitUserSelect option:checked").text;

            // Đổi tiêu đề
            document.getElementById("habitChartTitle").textContent =
                "Số thói quen theo ngày của " + selectedUser;

            // Gọi API lấy dữ liệu theo user
            fetch("?loadHabitUser=" + uid)
                .then(res => res.json())
                .then(data => {

                    const ctx = document.getElementById("habitChartUser").getContext("2d");

                    // Xóa chart cũ
                    if (habitChartInstance) {
                        habitChartInstance.destroy();
                    }

                    // Tạo chart mới
                    habitChartInstance = new Chart(ctx, {
                        type: "line",
                        data: {
                            labels: data.days,
                            datasets: [{
                                label: "Thói quen",
                                data: data.counts,
                                borderWidth: 2,
                                borderColor: "green",
                                tension: 0.3
                            }]
                        }
                    });

                });
            window.onload = function () {
                changeUserHabits();
            };
        }

    </script>

</body>

</html>