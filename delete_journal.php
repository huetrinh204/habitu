<?php
session_start();
include "config.php";

if(!isset($_SESSION["user_id"])){
    echo json_encode(['success'=>false,'message'=>'Chưa đăng nhập']); exit();
}

$user_id = $_SESSION["user_id"];

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['journal_id'])){
    $journal_id = intval($_POST['journal_id']);
    $stmt = $pdo->prepare("DELETE FROM health_journal WHERE journal_id=? AND user_id=?");
    $stmt->execute([$journal_id, $user_id]);
    echo json_encode(['success'=>$stmt->rowCount()>0]);
    exit();
}

echo json_encode(['success'=>false,'message'=>'Dữ liệu không hợp lệ']);
