<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: dangnhap.php");
    exit();
}

include "config.php";

$user_id  = $_SESSION["user_id"];
$username = $_SESSION["username"];
$today    = date('Y-m-d');

/* =========================
   LẤY DANH SÁCH THÓI QUEN
========================= */
$stmt = $pdo->prepare("SELECT * FROM habit WHERE user_id=?");
$stmt->execute([$user_id]);
$habits = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_habits = count($habits);

/* =========================
   STREAK CỦA USER
========================= */
$stmt = $pdo->prepare("SELECT total_streak FROM users WHERE user_id=?");
$stmt->execute([$user_id]);
$streak = (int)$stmt->fetchColumn();


/* =========================
   TẠO LOG HÔM NAY (NẾU CHƯA CÓ)
========================= */
$stmtCheck = $pdo->prepare("
    SELECT habit_id 
    FROM habit_logs 
    WHERE user_id=? AND log_date=?
");
$stmtCheck->execute([$user_id, $today]);
$loggedHabits = $stmtCheck->fetchAll(PDO::FETCH_COLUMN);

$stmtInsert = $pdo->prepare("
    INSERT INTO habit_logs(user_id, habit_id, log_date, completed)
    VALUES (?, ?, ?, 0)
");

foreach ($habits as $habit) {
    if (!in_array($habit['habit_id'], $loggedHabits)) {
        $stmtInsert->execute([$user_id, $habit['habit_id'], $today]);
    }
}

/* =========================
   THỐNG KÊ HÔM NAY
========================= */
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) AS total,
        SUM(completed = 1) AS done
    FROM habit_logs
    WHERE user_id=? AND log_date=?
");
$stmt->execute([$user_id, $today]);
$todayStat = $stmt->fetch(PDO::FETCH_ASSOC);

$total_today = (int)$todayStat['total'];
$done_today  = (int)$todayStat['done'];
$not_done_today = $total_today - $done_today;

$efficiency = $total_today > 0
    ? round($done_today / $total_today * 100)
    : 0;

/* =========================
   BAR CHART (THEO HABIT)
========================= */
$stmt = $pdo->prepare("
    SELECT habit_id, COUNT(*) AS total_done
    FROM habit_logs
    WHERE user_id=? AND completed='done'
    GROUP BY habit_id
");
$stmt->execute([$user_id]);
$doneMap = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$bar_labels = [];
$bar_data   = [];
foreach ($habits as $h) {
    $bar_labels[] = $h['habit_name'];
    $bar_data[]   = $doneMap[$h['habit_id']] ?? 0;
}

/* =========================
   LINE CHART (7 NGÀY)
========================= */
$week_days = [];
$week_data = [];

$stmt = $pdo->prepare("
    SELECT log_date, COUNT(*) AS done
    FROM habit_logs
    WHERE user_id=? AND completed='done'
      AND log_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
    GROUP BY log_date
");
$stmt->execute([$user_id]);
$weekMap = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i day"));
    $week_days[] = date('D', strtotime($date));
    $week_data[] = $weekMap[$date] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống Kê Thói Quen</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0)";>

<?php include "navbar.php"; ?>

<div class="mb-6 text-center mt-10">
    <h1 class="text-2xl font-bold text-white leading-loose">
        Thống Kê Thói Quen 📊
    </h1>
    <p class="text-sm text-white leading-relaxed">
        Xem tổng quan và tiến trình của bạn 🌟
    </p>
</div>

<section class="container mx-auto mt-10 px-4">
  <div class="bg-white/95 rounded-3xl shadow-xl p-7 max-w-5xl mx-auto">

    <div class="grid grid-cols-3 gap-4 justify-center mb-6">
      <div class="p-4 bg-teal-100 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-list-check text-teal-700 text-xl"></i>
        <div>
          <p class="text-sm text-gray-600">Tổng thói quen</p>
          <p class="font-bold text-lg"><?= $total_habits ?></p>
        </div>
      </div>

      <div class="p-4 bg-purple-100 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-bolt text-purple-700 text-xl"></i>
        <div>
          <p class="text-sm text-gray-600">Streak </p>
          <p class="font-bold text-lg"><?= $streak ?> ngày</p>
        </div>
      </div>

      <div class="p-4 bg-blue-100 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-percent text-blue-700 text-xl"></i>
        <div>
          <p class="text-sm text-gray-600">Hiệu suất</p>
          <p class="font-bold text-lg"><?= $efficiency ?>%</p>
        </div>
      </div>
    </div>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-2 gap-6">
      <div class="bg-white shadow-md p-4 rounded-xl border">
        <h3 class="font-semibold text-gray-700 mb-3">Chuỗi ngày theo thói quen</h3>
        <canvas id="barChart"></canvas>
      </div>

      <div class="bg-white shadow-md p-4 rounded-xl border">
        <h3 class="font-semibold text-gray-700 mb-3">Tỷ lệ hoàn thành hôm nay</h3>
        <canvas id="pieChart"></canvas>
      </div>
    </div>

    <div class="bg-white shadow-md p-4 rounded-xl border mt-6">
      <h3 class="font-semibold text-gray-700 mb-3">Tiến độ tuần này</h3>
      <canvas id="lineChart"></canvas>
    </div>

    <div class="bg-white shadow-md p-4 rounded-xl border mt-6">
      <h3 class="font-semibold text-gray-700 mb-4">Chi tiết thói quen</h3>
      <div class="space-y-4">
        <?php foreach ($habits as $habit): 
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM habit_logs WHERE habit_id=? AND user_id=? AND completed='done'");
            $stmt->execute([$habit['habit_id'], $user_id]);
            $done_count = $stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM habit_logs WHERE habit_id=? AND user_id=?");
            $stmt->execute([$habit['habit_id'], $user_id]);
            $total_count = $stmt->fetchColumn();
        ?>
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <i class="fa-solid <?= $habit['icon'] ?> text-blue-600"></i>
            <span><?= $habit['habit_name'] ?></span>
          </div>
          <span class="text-sm text-gray-500"><?= $done_count ?>/<?= $total_count ?> ngày</span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>
 <?php include "footer.php"; ?>
<script>
new Chart(barChart, {
  type: "bar",
  data: {
    labels: <?= json_encode($bar_labels) ?>,
    datasets: [{
      label: "Số ngày hoàn thành",
      data: <?= json_encode($bar_data) ?>,
      backgroundColor: "#14b8a6"
    }]
  }
});

new Chart(pieChart, {
  type: "pie",
  data: {
    labels: ["Đã hoàn thành", "Chưa hoàn thành"],
    datasets: [{
      data: [<?= $done_today ?>, <?= $not_done_today ?>],
      backgroundColor: ["#10b981", "#f97316"]
    }]
  }
});

new Chart(lineChart, {
  type: "line",
  data: {
    labels: <?= json_encode($week_days) ?>,
    datasets: [{
      label: "Hoàn thành",
      data: <?= json_encode($week_data) ?>,
      borderColor: "#14b8a6",
      tension: 0.3,
      fill: false
    }]
  }
});
</script>

</body>
</html>
