<?php
session_start();
include "config.php";

$data = json_decode(file_get_contents('php://input'), true);
$habitId = $data['habitId'];
$completed = $data['completed']; // 'done' hoặc 'missed'
$userId = $_SESSION['user_id'];
$today = date('Y-m-d');

// --- 1️⃣ Update hoặc insert habit log hôm nay ---
$stmt = $pdo->prepare("SELECT * FROM habit_logs WHERE habit_id=? AND user_id=? AND log_date=?");
$stmt->execute([$habitId, $userId, $today]);
$log = $stmt->fetch(PDO::FETCH_ASSOC);

if ($log) {
    $stmt = $pdo->prepare("UPDATE habit_logs SET completed=? WHERE habit_id=? AND user_id=? AND log_date=?");
    $stmt->execute([$completed, $habitId, $userId, $today]);
} else {
    $stmt = $pdo->prepare("INSERT INTO habit_logs (habit_id, user_id, log_date, completed) VALUES (?,?,?,?)");
    $stmt->execute([$habitId, $userId, $today, $completed]);
}

// --- 2️⃣ Cập nhật streak habit ---
$stmt = $pdo->prepare("SELECT current_streak, last_completed_date FROM habit WHERE habit_id=? AND user_id=?");
$stmt->execute([$habitId, $userId]);
$habit = $stmt->fetch(PDO::FETCH_ASSOC);

$current_streak = (int)($habit['current_streak'] ?? 0);
$last_completed = $habit['last_completed_date'] ?? null;

if ($completed == 'done') {
    if ($last_completed != $today) {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $current_streak = ($last_completed == $yesterday) ? $current_streak + 1 : 1;
        $stmt = $pdo->prepare("UPDATE habit SET current_streak=?, last_completed_date=? WHERE habit_id=? AND user_id=?");
        $stmt->execute([$current_streak, $today, $habitId, $userId]);
    }
} else {
    $current_streak = 0;
    $stmt = $pdo->prepare("UPDATE habit SET current_streak=?, last_completed_date=NULL WHERE habit_id=? AND user_id=?");
    $stmt->execute([$current_streak, $habitId, $userId]);
}

// --- 3️⃣ Tính completed_today và tổng habit ---
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM habit 
    WHERE status='Mẫu' OR (status='Người dùng' AND user_id=?)
");
$stmt->execute([$userId]);
$total_habits = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM habit_logs 
    WHERE user_id=? AND log_date=? AND completed='done'
");
$stmt->execute([$userId, $today]);
$completed_today = (int)$stmt->fetchColumn();

// --- 4️⃣ Cập nhật total_streak nếu hoàn thành tất cả habit hôm nay ---
$stmt = $pdo->prepare("SELECT total_streak, last_streak_update FROM users WHERE user_id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$total_streak = (int)($user['total_streak'] ?? 0);
$last_streak_update = $user['last_streak_update'] ?? null;

if ($completed_today === $total_habits && $last_streak_update != $today) {
    $total_streak += 1;
    $stmt = $pdo->prepare("UPDATE users SET total_streak=?, last_streak_update=? WHERE user_id=?");
    $stmt->execute([$total_streak, $today, $userId]);
}

// --- 5️⃣ Trả về JSON ---
echo json_encode([
    'success' => true,
    'current_streak' => $current_streak,
    'total_streak' => $total_streak,
    'completed_today' => $completed_today,
    'total_habits' => $total_habits
]);
?>
