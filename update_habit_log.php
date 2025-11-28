<?php
session_start();
include "config.php";

$data = json_decode(file_get_contents('php://input'), true);
$habitId = $data['habitId'];
$completed = $data['completed'];
$userId = $_SESSION['user_id'];
$today = date('Y-m-d');

// 1. Update hoặc insert log hôm nay
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

// 2. Lấy habit info
$stmt = $pdo->prepare("SELECT current_streak, last_completed_date FROM habit WHERE habit_id=?");
$stmt->execute([$habitId]);
$habit = $stmt->fetch(PDO::FETCH_ASSOC);

$current_streak = (int)($habit['current_streak'] ?? 0);
$last_completed = $habit['last_completed_date'] ?? null;

// 3. Tính streak thói quen
if ($completed == 'done') {
    if ($last_completed != $today) {
        if ($last_completed == date('Y-m-d', strtotime('-1 day'))) {
            $current_streak += 1;
        } else {
            $current_streak = 1;
        }
        $stmt = $pdo->prepare("UPDATE habit SET current_streak=?, last_completed_date=? WHERE habit_id=?");
        $stmt->execute([$current_streak, $today, $habitId]);
    }
} else {
    // Nếu bỏ tick (missed), reset streak = 0
    $current_streak = 0;
    $stmt = $pdo->prepare("UPDATE habit SET current_streak=?, last_completed_date=? WHERE habit_id=?");
    $stmt->execute([$current_streak, null, $habitId]);
}

// 4. Lấy tổng chuỗi ngày user và last_streak_update
$stmt = $pdo->prepare("SELECT total_streak, last_streak_update FROM user WHERE user_id=?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$total_streak = (int)($user['total_streak'] ?? 0);
$last_streak_update = $user['last_streak_update'] ?? null;

// 5. Cập nhật tổng chuỗi ngày: chỉ cộng 1 lần/ngày khi hoàn thành đủ thói quen hôm nay
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM habit h
    JOIN habit_logs hl ON h.habit_id = hl.habit_id
    WHERE hl.user_id=? AND hl.log_date=? AND hl.completed='done'
      AND (h.status='Mẫu' OR (h.status='Người dùng' AND h.user_id=?))
");
$stmt->execute([$userId, $today, $userId]);
$completed_today = (int)$stmt->fetchColumn();

// Tổng thói quen
$stmt = $pdo->prepare("SELECT COUNT(*) FROM habit WHERE status='Mẫu' OR (status='Người dùng' AND user_id=?)");
$stmt->execute([$userId]);
$total_habits = (int)$stmt->fetchColumn();

// Nếu completed_today = total_habits và chưa cập nhật streak hôm nay → cộng 1
if ($completed_today === $total_habits && $last_streak_update != $today) {
    $total_streak += 1;
    $stmt = $pdo->prepare("UPDATE user SET total_streak=?, last_streak_update=? WHERE user_id=?");
    $stmt->execute([$total_streak, $today, $userId]);
}

// 6. Trả về JSON
echo json_encode([
    'success' => true,
    'current_streak' => $current_streak,
    'total_streak' => $total_streak,
    'completed_today' => $completed_today,
    'total_habits' => $total_habits
]);
?>
