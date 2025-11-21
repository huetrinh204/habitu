<!-- NAV -->
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Habitu 🐱</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<nav class="flex justify-between px-8 py-3 items-center 
     bg-gradient-to-r from-purple-600 to-pink-500 text-white shadow-lg rounded-b-3xl">

    <div class="flex items-center gap-3">
        <img style="border-radius: 60%;" src="assets/logo_habitu.png" width="38" alt="logo">
        <h2 class="text-xl font-bold">Habitu</h2>
    </div>

    <ul class="flex gap-8 font-medium">
        <li class="flex items-center gap-1">
            <i class="fas fa-home"></i>
            <a href="dashboard.php" class="hover:text-yellow-300 transition">Trang Chủ</a></li>

        <li class="flex items-center gap-1">
            <i class="fas fa-book"></i>
            <a href="journal.php" class="hover:text-yellow-300 transition">Nhật Ký</a></li>

        <li class="flex items-center gap-1">
            <i class="fas fa-users"></i>
            <a href="community.php" class="hover:text-yellow-300 transition">Cộng Đồng</a></li>

        <li class="flex items-center gap-1">
            <i class="fas fa-chart-bar"></i>
            <a href="thongke.php" class="hover:text-yellow-300 transition">Thống Kê</a></li>

        <li class="flex items-center gap-1">
            <i class="fas fa-user"></i>
            <a href="account.php" class="hover:text-yellow-300 transition">Tài Khoản</a></li>

        <li class="flex items-center gap-1">
            <i class="fas fa-question-circle"></i>
            <a href="support.php" class="hover:text-yellow-300 transition">Hỗ Trợ</a>
        </li>
    </ul>

   <div class="relative inline-block group">
  <!-- Nút username -->
  <button class="flex items-center gap-2 text-white focus:outline-none">
    <i class="fas fa-user-circle text-xl"></i>
    <span><?php echo htmlspecialchars($username); ?></span>
    <i class="fas fa-chevron-down text-sm"></i>
  </button>

  <!-- Dropdown -->
  <div class="absolute right-0 mt-2 w-max bg-gradient-to-r from-purple-600 to-pink-500 rounded-xl py-2 px-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 shadow-lg">
    <a href="home.html" class="flex items-center gap-2 px-4 py-2 text-white hover:text-yellow-300 hover:bg-transparent transition-all rounded-lg whitespace-nowrap">
      <i class="fas fa-sign-out-alt"></i>
      Đăng xuất
    </a>
  </div>
</div>
</nav>