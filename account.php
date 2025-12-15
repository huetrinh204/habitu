<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: dangnhap.php");
    exit();
}

include "config.php";

$user_id = $_SESSION["user_id"];

/* =========================
   LẤY THÔNG TIN USER
========================= */
$stmt = $pdo->prepare("SELECT username, email, password, gender, tel FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* =========================
   CẬP NHẬT THÔNG TIN
========================= */
if (isset($_POST["update_profile"])) {
    $new_username = trim($_POST["username"]);
    $new_email = trim($_POST["email"]);
    $new_gender = $_POST["gender"];
    $new_tel = trim($_POST["tel"]);

    if ($new_username !== "" && $new_email !== "") {
        $stmt = $pdo->prepare(
            "UPDATE users 
     SET username = ?, email = ?, gender = ?, tel = ?
     WHERE user_id = ?"
        );
        $stmt->execute([
            $new_username,
            $new_email,
            $new_gender,
            $new_tel,
            $user_id
        ]);

        $_SESSION["username"] = $new_username;
        $_SESSION["success"] = "Cập nhật thông tin thành công 🎉";

        header("Location: account.php");
        exit();
    }
}

/* =========================
   ĐỔI MẬT KHẨU
========================= */
if (isset($_POST["change_password"])) {
    $old = $_POST["old_password"];
    $new = $_POST["new_password"];
    $confirm = $_POST["confirm_password"];

    if (!password_verify($old, $user["password"])) {
        $_SESSION["error"] = "Mật khẩu hiện tại không đúng ❌";
    } elseif ($new !== $confirm) {
        $_SESSION["error"] = "Xác nhận mật khẩu không khớp ❌";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            "UPDATE users SET password = ? WHERE user_id = ?"
        );
        $stmt->execute([$hash, $user_id]);

        $_SESSION["success"] = "Đổi mật khẩu thành công 🔐";
    }

    header("Location: account.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tài Khoản | Habitu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0);">

    <?php include "navbar.php"; ?>

    <!-- TIÊU ĐỀ -->
    <div class="mb-6 text-center mt-10">
        <h1 class="text-2xl font-bold text-white">
            Tài Khoản Cá Nhân 🐱
        </h1>
        <p class="text-sm text-white mt-2">
            Cập nhật thông tin và bảo mật tài khoản của bạn ✨
        </p>
    </div>

    <div class="max-w-4xl mx-auto px-6 space-y-6 pb-16">

        <!-- HỒ SƠ CÁ NHÂN -->
        <form method="post" class="bg-white/80 shadow-lg rounded-xl p-6">
            <h3 class="text-lg font-semibold mb-4 flex gap-2">
                <i class="fas fa-id-card text-blue-500"></i> Hồ Sơ Cá Nhân
            </h3>

            <label class="text-sm font-medium">Tên hiển thị</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user["username"]) ?>"
                class="w-full mt-1 mb-4 p-2 border rounded-lg">

            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>"
                class="w-full mt-1 mb-4 p-2 border rounded-lg">

            <label class="text-sm font-medium">Giới tính</label>
            <select name="gender" class="w-full mt-1 mb-4 p-2 border rounded-lg">
                <option value="">-- Chọn giới tính --</option>
                <option value="Nam" <?= ($user["gender"] ?? '') === "Nam" ? "selected" : "" ?>>Nam</option>
                <option value="Nữ" <?= ($user["gender"] ?? '') === "Nữ" ? "selected" : "" ?>>Nữ</option>
                <option value="Khác" <?= ($user["gender"] ?? '') === "Khác" ? "selected" : "" ?>>Khác</option>
            </select>
            
            <label class="text-sm font-medium">Số điện thoại</label>
            <input type="text" name="tel" value="<?= htmlspecialchars($user["tel"] ?? "") ?>"
                class="w-full mt-1 mb-4 p-2 border rounded-lg" placeholder="VD: 0123456789">

            <button name="update_profile" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                Cập Nhật Thông Tin
            </button>
        </form>

        <!-- ĐỔI MẬT KHẨU -->
        <form method="post" class="bg-white/80 shadow-lg rounded-xl p-6">
            <h3 class="text-lg font-semibold mb-4 flex gap-2">
                <i class="fas fa-lock text-orange-500"></i> Đổi Mật Khẩu
            </h3>

            <label class="text-sm font-medium">Mật khẩu hiện tại</label>
            <input type="password" name="old_password" class="w-full mt-1 mb-3 p-2 border rounded-lg">

            <label class="text-sm font-medium">Mật khẩu mới</label>
            <input type="password" name="new_password" class="w-full mt-1 mb-3 p-2 border rounded-lg">

            <label class="text-sm font-medium">Xác nhận mật khẩu mới</label>
            <input type="password" name="confirm_password" class="w-full mt-1 mb-4 p-2 border rounded-lg">

            <button name="change_password" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg">
                Đổi Mật Khẩu
            </button>
        </form>

    </div>

    <?php include "footer.php"; ?>

    <!-- ===================== POP-UP Ở GIỮA MÀN HÌNH ===================== -->
    <script>
        function showPopup(message, type = "success") {
            const overlay = document.createElement("div");
            overlay.style.position = "fixed";
            overlay.style.top = "0";
            overlay.style.left = "0";
            overlay.style.width = "100vw";
            overlay.style.height = "100vh";
            overlay.style.backgroundColor = "rgba(0,0,0,0.3)";
            overlay.style.display = "flex";
            overlay.style.alignItems = "center";
            overlay.style.justifyContent = "center";
            overlay.style.zIndex = "9999";

            const popup = document.createElement("div");
            popup.textContent = message;
            popup.className = `px-6 py-4 rounded-lg shadow-lg text-white text-center text-lg transition-all duration-500
                       ${type === "success" ? "bg-green-500" : "bg-red-500"}`;

            overlay.appendChild(popup);
            document.body.appendChild(overlay);

            // tự động biến mất sau 3 giây
            setTimeout(() => {
                overlay.style.opacity = '0';
                overlay.style.transition = "opacity 0.5s";
                setTimeout(() => overlay.remove(), 500);
            }, 3000);
        }

        <?php if (isset($_SESSION["success"])): ?>
            showPopup("<?= $_SESSION['success'] ?>", "success");
            <?php unset($_SESSION["success"]); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION["error"])): ?>
            showPopup("<?= $_SESSION['error'] ?>", "error");
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>
    </script>

</body>

</html>