<?php
include "../config.php"; // kết nối DB

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["habit_id"];
    $name = $_POST["habit_name"];
    $desc = $_POST["description"];
    $icon = $_POST["icon"];

    $stmt = $pdo->prepare("UPDATE habit SET habit_name=?, description=?, icon=? WHERE habit_id=?");
    $stmt->execute([$name, $desc, $icon, $id]);

    header("Location: habits.php"); // reload lại bảng
    exit;
}
?>