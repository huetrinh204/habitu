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

/* =============================
   LẤY SỐ LIỆU THỐNG KÊ
   ============================= */


// Tổng người dùng
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Tổng thói quen (của toàn hệ thống)
$totalHabits = $pdo->query("SELECT COUNT(*) FROM habit")->fetchColumn();

// Tổng bài viết
$totalPosts = $pdo->query("SELECT COUNT(*) FROM post")->fetchColumn();

// Tổng bình luận
$totalComments = $pdo->query("SELECT COUNT(*) FROM comment")->fetchColumn();


/* =============================
   LẤY DỮ LIỆU BIỂU ĐỒ
   ============================= */

// USERS PER DAY
$q1 = $pdo->query("
    SELECT DATE(create_acc) AS day, COUNT(*) AS total
    FROM users
    GROUP BY day
    ORDER BY day ASC
");

$userDays = [];
$userCounts = [];

while ($row = $q1->fetch(PDO::FETCH_ASSOC)) {
    $userDays[] = $row["day"];
    $userCounts[] = $row["total"];
}

// POSTS PER DAY
$q2 = $pdo->query("
    SELECT DATE(created_at) AS day, COUNT(*) AS total
    FROM post
    GROUP BY day
    ORDER BY day ASC
");

$postDays = [];
$postCounts = [];

while ($row = $q2->fetch(PDO::FETCH_ASSOC)) {
    $postDays[] = $row["day"];
    $postCounts[] = $row["total"];
}

// HABIT PER DAY
$q3 = $pdo->query("
    SELECT DATE(created_hb) AS day, COUNT(*) AS total
    FROM habit
    GROUP BY day
    ORDER BY day ASC
");

$habitDays = [];
$habitCounts = [];

while ($row = $q3->fetch(PDO::FETCH_ASSOC)) {
    $habitDays[] = $row["day"];
    $habitCounts[] = $row["total"];
}

//COMMENT PER DAY
$q4 = $pdo->query("
    SELECT DATE(created_cmt) AS day, COUNT(*) AS total
    FROM comment
    GROUP BY day
    ORDER BY day ASC
");

$commentDays = [];
$commentCounts = [];

while ($row = $q4->fetch(PDO::FETCH_ASSOC)) {
    $commentDays[] = $row["day"];
    $commentCounts[] = $row["total"];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Habitu Dashboard 🐱</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.habit-box:hover {
    transform: translateY(-4px);
}
</style>
</head>

<body style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0)">

<!-- NAV -->
<?php include "navbar.php"?>

<div class="px-10 py-5">
    <h1 class="text-3xl font-bold" style="color:#ffffff; text-shadow: 2px 2px 6px rgba(0,0,0,0.5);">Dashboard</h1>
    <p class="text-gray-700 mb-6">Quản lý tổng quan hoạt động người dùng</p>
 </div>
<!-- METRICS -->
<div class="grid grid-cols-4 gap-6 px-10">

    <div class="p-6 bg-white shadow rounded-2xl text-center habit-box transition">
        <i class="fas fa-users text-3xl text-purple-600 mb-2"></i>
        <h3 class="text-xl font-bold"><?php echo $totalUsers; ?></h3>
        <p class="text-gray-600">Tổng người dùng</p>
    </div>

    <div class="p-6 bg-white shadow rounded-2xl text-center habit-box transition">
        <i class="fas fa-pen text-3xl text-pink-500 mb-2"></i>
        <h3 class="text-xl font-bold"><?php echo $totalPosts; ?></h3>
        <p class="text-gray-600">Tổng bài viết</p>
    </div>

    <div class="p-6 bg-white shadow rounded-2xl text-center habit-box transition">
        <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
        <h3 class="text-xl font-bold"><?php echo $totalHabits; ?></h3>
        <p class="text-gray-600">Tổng thói quen</p>
    </div>

    <div class="p-6 bg-white shadow rounded-2xl text-center habit-box transition">
        <i class="fas fa-comment text-3xl text-blue-500 mb-2"></i>
        <h3 class="text-xl font-bold"><?php echo $totalComments; ?></h3>
        <p class="text-gray-600">Tổng bình luận</p>
    </div>

</div>

<!-- CHARTS -->
<div class="grid grid-cols-2 gap-8 px-10 mt-12 mb-16">

    <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="text-lg font-bold mb-3 text-purple-700">Tăng trưởng người dùng theo ngày</h3>
        <canvas id="userChart" height="120"></canvas>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="text-lg font-bold mb-3 text-pink-600">Số bài viết theo ngày</h3>
        <canvas id="postChart" height="120"></canvas>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="text-lg font-bold mb-3 text-green-600">Số thói quen theo ngày</h3>
        <canvas id="habitChart" height="120"></canvas>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow">
        <h3 class="text-lg font-bold mb-3 text-red-600">Số bình luận theo ngày</h3>
        <canvas id="commentChart" height="120"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ---------- USER GROWTH PER DAY ---------- */
new Chart(document.getElementById('userChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($userDays); ?>,
        datasets: [{
            label: 'Người dùng mới mỗi ngày',
            data: <?php echo json_encode($userCounts); ?>,
            borderWidth: 2,
            tension: 0.3
        }]
    }
});

/* ---------- POSTS PER DAY ---------- */
new Chart(document.getElementById('postChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($postDays); ?>,
        datasets: [{
            label: 'Bài viết mỗi ngày',
            data: <?php echo json_encode($postCounts); ?>,
            borderWidth: 2
        }]
    }
});

/* ---------- HABITS PER DAY ---------- */
new Chart(document.getElementById('habitChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($habitDays); ?>,
        datasets: [{
            label: 'Thói quen tạo mới mỗi ngày',
            data: <?php echo json_encode($habitCounts); ?>,
            borderWidth: 2,
            tension: 0.3
        }]
    }
});

/* ---------- COMMENTS PER DAY ---------- */
new Chart(document.getElementById('commentChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($commentDays); ?>,
        datasets: [{
            label: 'Bình luận mỗi ngày',
            data: <?php echo json_encode($commentCounts); ?>,
            borderWidth: 2
        }]
    }
});
</script>

</body>
</html>