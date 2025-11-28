<?php
include "config.php";
session_start();
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$habit_id = $data['habitId'];
$completed = $data['completed'];
$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT log_id FROM habit_logs WHERE habit_id=? AND user_id=? AND log_date=?");
$stmt->execute([$habit_id,$user_id,$today]);
$log = $stmt->fetch(PDO::FETCH_ASSOC);

if($log){
    $stmt = $pdo->prepare("UPDATE habit_logs SET completed=? WHERE log_id=?");
    $stmt->execute([$completed, $log['log_id']]);
}else{
    $stmt = $pdo->prepare("INSERT INTO habit_logs (habit_id, user_id, log_date, completed) VALUES (?,?,?,?)");
    $stmt->execute([$habit_id,$user_id,$today,$completed]);
}

echo json_encode(['success'=>true]);
