<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = $_SESSION["username"] ?? "User";
?>


<!-- NAV -->
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Habitu 🐱</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    .habit-box:hover { transform: translateY(-4px); }

    /* Mobile menu animation */
    .mobile-menu {
      max-height: 0;
      overflow: hidden;
      transition: max-height .3s ease;
    }
    .mobile-menu.open {
      max-height: 500px;
    }
  </style>
</head>

<nav class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white shadow-lg rounded-b-3xl">

  <div class="flex justify-between items-center">

    <!-- Logo + tên -->
    <div class="flex items-center gap-3">
      <img src="assets/logo_habitu.png" width="38" class="rounded-full" />
      <h2 class="text-xl font-bold">Habitu</h2>
    </div>

    <!-- Menu Desktop -->
    <ul class="hidden md:flex gap-8 font-medium">
      <li class="flex items-center gap-1"><i class="fas fa-home"></i><a href="dashboard.php" class="hover:text-yellow-300">Thói Quen</a></li>
      <li class="flex items-center gap-1"><i class="fas fa-book"></i><a href="journal.php" class="hover:text-yellow-300">Nhật Ký</a></li>
      <li class="flex items-center gap-1"><i class="fas fa-users"></i><a href="community.php" class="hover:text-yellow-300">Cộng Đồng</a></li>
      <li class="flex items-center gap-1"><i class="fas fa-chart-bar"></i><a href="thongke.php" class="hover:text-yellow-300">Thống Kê</a></li>
      <li class="flex items-center gap-1"><i class="fas fa-user"></i><a href="account.php" class="hover:text-yellow-300">Tài Khoản</a></li>
      <li class="flex items-center gap-1"><i class="fas fa-question-circle"></i><a href="support.php" class="hover:text-yellow-300">Hỗ Trợ</a></li>
    </ul>

    <!-- Dropdown User -->
    <div class="hidden md:block relative group">
      <button class="flex items-center gap-2">
        <i class="fas fa-user-circle text-xl"></i>
        <span><?= htmlspecialchars($username) ?></span>
        <i class="fas fa-chevron-down text-sm"></i>
      </button>

      <div class="absolute right-0 mt-2 w-max bg-gradient-to-r from-purple-600 to-pink-500 rounded-xl py-2 px-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 shadow-lg">
        <a href="home.html" class="flex items-center gap-2 px-4 py-2 hover:text-yellow-300 whitespace-nowrap">
          <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
      </div>
    </div>

    <!-- Hamburger Button (Mobile Only) -->
    <button id="menuBtn" class="md:hidden text-2xl">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Menu -->
  <div id="mobileMenu" class="mobile-menu md:hidden mt-3 bg-white/10 rounded-xl overflow-hidden">

    <ul class="flex flex-col p-4 gap-3 text-white font-medium">
      <li><a href="dashboard.php" class="flex items-center gap-2"><i class="fas fa-home"></i> Thói Quen</a></li>
      <li><a href="journal.php" class="flex items-center gap-2"><i class="fas fa-book"></i> Nhật Ký</a></li>
      <li><a href="community.php" class="flex items-center gap-2"><i class="fas fa-users"></i> Cộng Đồng</a></li>
      <li><a href="thongke.php" class="flex items-center gap-2"><i class="fas fa-chart-bar"></i> Thống Kê</a></li>
      <li><a href="account.php" class="flex items-center gap-2"><i class="fas fa-user"></i> Tài Khoản</a></li>
      <li><a href="support.php" class="flex items-center gap-2"><i class="fas fa-question-circle"></i> Hỗ Trợ</a></li>

      <hr class="border-white/30" />

      <li><a href="home.html" class="flex items-center gap-2"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
    </ul>

  </div>

</nav>


<script>
  const menuBtn = document.getElementById("menuBtn");
  const mobileMenu = document.getElementById("mobileMenu");

  menuBtn.onclick = () => {
    mobileMenu.classList.toggle("open");
  };
</script>