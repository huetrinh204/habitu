<?php
session_start();
require "config.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

$email = "";
$popup = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        // Lấy user theo email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $popup = "Email chưa được đăng ký!";
        } elseif (!password_verify($password, $user["password"])) {
            $popup = "Sai mật khẩu!";
        } else {
            // Lưu session
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            // Cập nhật last_activity
            $pdo->prepare("UPDATE users SET last_activity = NOW() WHERE user_id = ?")
                ->execute([$user["user_id"]]);

            $popup = "Đăng nhập thành công!";
            $success = true;
        }
    } catch (PDOException $e) {
        $popup = "Lỗi: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng Nhập</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(to right, #00c6ff, #0072ff);
  margin: 0;
  padding: 0;
  min-height: 100vh;
}

.container {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  width: 360px;
  max-width: 90%;
  padding: 40px 30px;
  margin: 40px auto;
  text-align: center;
}

.container .logo,
.container h2,
.container > p:first-of-type {
  text-align: center;
}

.container .logo img {
  display: block;
  margin: 0 auto 10px;
}

h2 { margin: 10px 0 5px; color: #00bfff; }
p { font-size: 14px; color: #666; margin-bottom: 20px; }
form { text-align: left; }
label { display: block; font-size: 13px; margin-bottom: 5px; color: #333; }
input[type="email"],
input[type="password"] {
  width: 100%; padding: 10px; margin-bottom: 15px;
  border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box; font-size: 14px;
}
.options {
  display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;
}
.options a { font-size: 13px; color: #00bfff; text-decoration: none; }
.btn {
  width: 100%; padding: 10px; background-color: #00bfff; color: white; border: none; border-radius: 6px;
  cursor: pointer; font-size: 15px; margin-bottom: 15px;
}
.btn:hover { background-color: #0099cc; }
.divider { text-align: center; margin: 10px 0; color: #aaa; font-size: 13px; }
.social-btn {
  width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;
  margin-bottom: 10px; cursor: pointer; background-color: #fff; font-size: 14px;
}
.social-btn.google { color: #db4437; }
.social-btn.facebook { color: #4267B2; }
.signup-link { font-size: 13px; color: #666; text-align: center; }
.signup-link a { color: #00bfff; text-decoration: none; font-weight: 600; }

/* popup */
#popup {
  display: none; position: fixed; top:0; left:0; width:100%; height:100%;
  background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index:9999;
}
#popup .box {
  background: white; padding: 20px 30px; border-radius: 8px;
  text-align: center; max-width: 300px; position: relative;
}
#popup .box button.close {
  position: absolute; top:5px; right:8px; border:none; background:none; font-size:18px; cursor:pointer;
}
  </style>
</head>
<body>
  <div class="container">
    <div class="logo"><img src="assets/logo_habitu.png" width="120" height="120" alt="Habitu Logo" class="logo-img"></div>
    <h2>Đăng Nhập</h2>
    <p>Chào mừng trở lại! Hãy tiếp tục hành trình của bạn 🐾</p>

    <form action="" method="POST">
      <label>Email</label>
      <input type="email" name="email" placeholder="your@email.com" required value="<?= htmlspecialchars($email) ?>">

      <label>Mật khẩu</label>
      <input type="password" name="password" placeholder="********" required>

      <div class="options">
        <label><input type="checkbox"> Ghi nhớ đăng nhập</label>
        <a href="#">Quên mật khẩu?</a>
      </div>

      <button type="submit" class="btn">Đăng Nhập</button>

      <div class="divider">Hoặc</div>

      <button type="button" class="social-btn google">Đăng nhập với Google</button>
      <button type="button" class="social-btn facebook">Đăng nhập với Facebook</button>
    </form>

    <p class="signup-link">Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a></p>
  </div>

  <!-- popup -->
  <div id="popup">
    <div class="box">
      <button class="close" onclick="document.getElementById('popup').style.display='none'">&times;</button>
      <p id="popupText"></p>
    </div>
  </div>

<script>
  <?php if(!empty($popup)): ?>
    const popup = document.getElementById('popup');
    const popupText = document.getElementById('popupText');
    popupText.innerText = "<?= $popup ?>";
    popup.style.display = "flex";
    <?php if($success): ?>
      setTimeout(function(){ window.location.href='index.html'; }, 1000);
    <?php endif; ?>
  <?php endif; ?>
</script>
</body>
</html>
