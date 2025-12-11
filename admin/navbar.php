<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<nav class="bg-gradient-to-r from-purple-600 to-pink-500 text-white shadow-lg px-6 py-3">

  <div class="max-w-7xl mx-auto flex justify-between items-center">

      <!-- Left: LOGO -->
      <div class="flex items-center gap-3">
          <img style="border-radius: 60%" src="assets/images/logo_habitu.png" width="38">
          <h2 class="text-xl font-bold flex items-center gap-2">
            Habitu 
            <span class="bg-yellow-400 text-black px-2 py-0.5 rounded text-sm">ADMIN</span>
          </h2>
      </div>

      <!-- Center: MENU (desktop) -->
      <ul id="menuList" class="hidden md:flex gap-8 font-medium">
          <li class="flex items-center gap-1">
              <i class="fas fa-tachometer-alt"></i>
              <a href="index.php" class="hover:text-yellow-300 transition">Dashboard</a>
          </li>

          <li class="flex items-center gap-1">
              <i class="fas fa-user"></i>
              <a href="users.php" class="hover:text-yellow-300 transition">Người Dùng</a>
          </li>

          <li class="flex items-center gap-1">
              <i class="fas fa-file-alt"></i>
              <a href="post.php" class="hover:text-yellow-300 transition">Bài Viết</a>
          </li>

          <li class="flex items-center gap-1">
              <i class="fas fa-redo"></i>
              <a href="habits.php" class="hover:text-yellow-300 transition">Thói Quen</a>
          </li>

          <li class="flex items-center gap-1">
              <i class="fas fa-cog"></i>
              <a href="settings.php" class="hover:text-yellow-300 transition">Cài Đặt</a>
          </li>
      </ul>

      <!-- Right: ADMIN + MOBILE BUTTON -->
      <div class="flex items-center gap-4">

          <!-- Dropdown Admin -->
          <div class="relative hidden md:block">
              <span id="adminDropdownBtn" class="flex items-center gap-1 cursor-pointer select-none">
                  <i class="fas fa-user-circle text-xl"></i>
                  <span><?php echo htmlspecialchars($username); ?></span>
                  <i class="fas fa-chevron-down text-sm"></i>
              </span>

              <div id="adminDropdown" class="absolute right-0 mt-2 w-44 bg-gradient-to-r from-purple-600 to-pink-500
                  text-white rounded-lg shadow-lg hidden z-50">
                  <a href="../home.html" class="flex items-center gap-2 px-4 py-2 hover:bg-purple-700 rounded-lg transition">
                      <i class="fas fa-sign-out-alt"></i> Đăng xuất
                  </a>
              </div>
          </div>

          <!-- Nút menu mobile -->
          <button id="menuBtn" class="md:hidden text-2xl">
              <i class="fas fa-bars"></i>
          </button>

      </div>

  </div>

  <!-- MENU MOBILE (sẽ hiện ở dưới nav) -->
  <ul id="menuMobile" class="hidden md:hidden flex-col gap-4 mt-3 p-4 
      bg-gradient-to-r from-purple-600 to-pink-500 rounded-xl text-white">

      <li><a href="index.php">Dashboard</a></li>
      <li><a href="users.php">Người Dùng</a></li>
      <li><a href="post.php">Bài Viết</a></li>
      <li><a href="habits.php">Thói Quen</a></li>
      <li><a href="settings.php">Cài Đặt</a></li>
  </ul>

</nav>


<!-- JS -->
<script>
  const menuBtn = document.getElementById("menuBtn");
  const menuList = document.getElementById("menuList");

  menuBtn.addEventListener("click", () => {
      menuList.classList.toggle("hidden");
      menuList.classList.toggle("flex");
      menuList.classList.toggle("flex-col");
      menuList.classList.toggle("bg-gradient-to-r");
      menuList.classList.toggle("from-purple-600");
      menuList.classList.toggle("to-pink-500");
      menuList.classList.toggle("p-4");
      menuList.classList.toggle("rounded-xl");
      menuList.classList.toggle("mt-3");
  });

  // Dropdown Admin
  const btn = document.getElementById('adminDropdownBtn');
  const dropdown = document.getElementById('adminDropdown');

  btn.addEventListener('click', () => {
    dropdown.classList.toggle('hidden');
  });

  window.addEventListener('click', (e) => {
    if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.add('hidden');
    }
  });
</script>