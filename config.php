<?php
$host = "localhost";
$dbname = "habitly_db";
$user = "root";
$pass = "";

try {
    // Chuyển charset sang utf8mb4 để hỗ trợ emoji
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Đảm bảo kết nối sử dụng utf8mb4
    $pdo->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
?>
