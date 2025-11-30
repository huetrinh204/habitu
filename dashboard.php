
<?php
// ---- KHỞI TẠO SESSION MỘT LẦN DUY NHẤT ----
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "config.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location: dangnhap.php");
    exit();
}

// Lấy user_id & username
$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

/* ==================== LẤY DANH SÁCH THÓI QUEN ==================== */
$stmt = $pdo->prepare("
      SELECT *, 0 AS current_streak  FROM habit 
    WHERE status='Mẫu' OR (status='Người dùng' AND user_id=:user_id) 
    ORDER BY created_hb DESC
");
$stmt->execute(['user_id' => $user_id]);
$habits = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ==================== 1. TẠO THÓI QUEN NGƯỜI DÙNG ==================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user_habit'])) {

    $name = $_POST['habit_name'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];

    $stmt = $pdo->prepare("
        INSERT INTO habit 
        (habit_name, description, icon, status, created_hb, user_id, current_streak, last_completed_date)
        VALUES (?, ?, ?, 'Người dùng', NOW(), ?, 0, NULL)
    ");

    $stmt->execute([$name, $description, $icon, $user_id]);
    header("Location: dashboard.php");
    exit;
}



/* ==================== 2. XOÁ THÓI QUEN ==================== */
if (isset($_GET['delete_user_habit'])) {
    $habit_id = $_GET['delete_user_habit'];

    $stmt = $pdo->prepare("DELETE FROM habit WHERE habit_id=? AND user_id=?");
    $stmt->execute([$habit_id, $user_id]);

    header("Location: dashboard.php");
    exit;
}

/* ==================== 3. CẬP NHẬT THÓI QUEN ==================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user_habit'])) {

    $habit_id = $_POST['habit_id'];
    $name     = $_POST['habit_name'];
    $desc     = $_POST['description'];
    $icon     = $_POST['icon'];

    $stmt = $pdo->prepare("
        UPDATE habit 
        SET habit_name=?, description=?, icon=? 
        WHERE habit_id=? AND user_id=?
    ");

    $stmt->execute([$name, $desc, $icon, $habit_id, $user_id]);

    header("Location: dashboard.php");
    exit;
}

$today = date('Y-m-d');

/*==================== 1. Lấy tất cả habit ====================*/
// Lấy tất cả thói quen mẫu + thói quen người dùng
$stmt = $pdo->prepare("
    SELECT * FROM habit 
    WHERE status='Mẫu' OR (status='Người dùng' AND user_id=:user_id)
    ORDER BY created_hb DESC
");
$stmt->execute(['user_id' => $user_id]);
$habits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tổng thói quen
$total_habits = count($habits);

/*==================== 2. Lấy completed hôm nay ====================*/
// Lấy tất cả log hôm nay của user
$stmt = $pdo->prepare("
    SELECT habit_id, completed 
    FROM habit_logs 
    WHERE user_id=? AND log_date=?
");
$stmt->execute([$user_id, $today]);
$habit_logs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // habit_id => completed

// Số thói quen đã done hôm nay
$completed_today = count(array_filter($habit_logs, fn($c)=>$c=='done'));

/*==================== 3. Lấy tổng chuỗi ngày ====================*/
// Lấy total_streak từ bảng users
$stmt = $pdo->prepare("SELECT total_streak FROM users WHERE user_id=?");
$stmt->execute([$user_id]);
$total_streak = (int)$stmt->fetchColumn();

/*==================== 4. Lấy streak từng habit ====================*/
// Chuẩn bị mảng streak của từng habit để hiển thị
$habit_streaks = [];
foreach($habits as $hb){
    $habit_id = $hb['habit_id'];
    $stmt = $pdo->prepare("SELECT current_streak FROM habit WHERE habit_id=? AND user_id=?");
    $stmt->execute([$habit_id, $user_id]);
    $habit_streaks[$habit_id] = (int)$stmt->fetchColumn();
}

?>


<!DOCTYPE html>
<html lang="vi">

<body class="bg-gradient-to-br from-cyan-300 to-teal-400 min-h-screen">


<!-- NAV -->
<?php include "navbar.php"; ?>

<!-- Header -->
<header class="welcome text-center py-6">
  <h2 class="text-2xl font-semibold text-gray-700" style="color: white;">Chào mừng bạn đến với Habitu! 🐱✨</h2>
  <p class="text-gray-600">Hãy tiếp tục xây dựng thói quen lành mạnh của bạn!</p>
</header>

<!-- Stats -->
<section class="stats container mx-auto grid grid-cols-3 gap-6 px-6">
  <div class="stat-box bg-white shadow-md p-5 rounded-lg flex items-center gap-4">
    <img style="border-radius:60%;width:50px;height:50px;" src="assets/icons/sun.png" alt="icon">
    <div>
      <h3 class="font-semibold">Tổng thói quen</h3>
      <p class="text-lg font-bold"><?= $total_habits ?></p>
    </div>
  </div>

  <div class="stat-box bg-white shadow-md p-5 rounded-lg flex items-center gap-4">
    <img style="border-radius:60%;width:50px;height:50px;" src="assets/icons/check.png" alt="icon">
    <div>
      <h3 class="font-semibold">Hoàn thành hôm nay</h3>
      <p class="text-lg font-bold">
  <span id="completedToday"><?= $completed_today ?></span>/<?= $total_habits ?> 
  (<span id="completedPercent"><?= $total_habits ? round($completed_today/$total_habits*100) : 0 ?></span>%)
</p>
    </div>
  </div>

  <div class="stat-box bg-white shadow-md p-5 rounded-lg flex items-center gap-4">
    <img style="border-radius:60%;width:50px;height:50px;" src="assets/icons/streak.png" alt="icon">
    <div>
      <h3 class="font-semibold">Tổng chuỗi ngày</h3>
    <p class="text-lg font-bold" id="totalStreak"><?= $total_streak ?> ngày</p>


    </div>
  </div>
</section>



<!-- Habits -->
<section class="habits-section px-6 mt-8">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold text-gray-800">Thói Quen Của Bạn</h3>
    <button id="addHabitBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
      + Thêm Thói Quen
    </button>
  </div>

 <div id="habitList" class="habit-list">
<?php foreach ($habits as $hb): ?>
    <div class="habit-item bg-white p-4 rounded-xl shadow-md mb-3 flex items-center gap-3 relative">

        <!-- Checkbox -->
        <input type="checkbox" class="habit-checkbox" 
       data-habit-id="<?= $hb['habit_id'] ?>"
       <?= isset($habit_logs[$hb['habit_id']]) && $habit_logs[$hb['habit_id']] === 'done' ? 'checked' : '' ?>>


        <!-- Icon -->
        <div class="text-3xl"><?= htmlspecialchars($hb['icon']) ?></div>

        <!-- Tên + mô tả -->
        <div>
            <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($hb['habit_name']) ?></h4>
            <p class="text-gray-500 text-sm"><?= htmlspecialchars($hb['description']) ?></p>
        </div>

        <!-- Chuỗi streak -->
        <div class="streak text-orange-400 font-semibold ml-auto mr-3"></div>
        <!-- Nút sửa / xoá -->
        <div class="flex gap-2">
            <!-- Nút sửa -->
            <button 
                onclick="openEditHabit('<?= $hb['habit_id'] ?>', '<?= htmlspecialchars($hb['habit_name']) ?>', '<?= htmlspecialchars($hb['description']) ?>', '<?= htmlspecialchars($hb['icon']) ?>')"
                class="text-blue-600 hover:text-blue-800 font-semibold">
                ✏️
            </button>

            <!-- Nút xoá -->
            <a href="dashboard.php?delete_user_habit=<?= $hb['habit_id'] ?>"
               onclick="return confirm('Xóa thói quen này?')"
               class="text-red-600 hover:text-red-800 font-semibold">
                🗑️
            </a>
        </div>

    </div>
<?php endforeach; ?>
</div>
</section>

<!-- POPUP THÊM THÓI QUEN -->

<div id="createHabitModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-11/12 md:w-1/2 p-6 relative">

        <!-- Header -->
        <div class="mb-4 p-4 rounded-t-2xl bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold text-xl flex justify-between items-center">
            Tạo Thói Quen
            <button id="closeModalBtn" class="text-white text-2xl font-bold hover:text-gray-200">&times;</button>
        </div>

        <form method="POST" class="space-y-4">
            <!-- Flag để PHP biết là form user -->
            <input type="hidden" name="create_user_habit" value="1">

            <!-- Tên thói quen -->
            <div>
                <label class="block font-medium mb-1">Tên Thói Quen</label>
                <input name="habit_name" type="text" 
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-pink-400" 
                    required>
            </div>

            <!-- Mô tả -->
            <div>
                <label class="block font-medium mb-1">Mô tả</label>
                <textarea name="description"
                    class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-pink-400"
                    required></textarea>
            </div>

            <!-- Chọn Icon -->
            <div class="relative">
                <label class="block font-medium mb-1">Chọn Icon</label>

                <input id="iconInput" type="text" readonly placeholder="Chọn icon..."
                       class="w-full border border-gray-300 px-3 py-2 rounded-lg cursor-pointer focus:ring-2 focus:ring-pink-400" required>

                <!-- Icon Grid -->
                <div id="iconGrid"
                    class="absolute top-full left-0 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 grid grid-cols-5 gap-2 hidden max-h-48 overflow-y-auto z-50">

                    <!-- 25 icon -->
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">💧</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🏃</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">📚</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🧘</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">😴</div>

                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🗣️</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">💰</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">📝</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🎧</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🎨</div>

                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">📖</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">⚽</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🏊</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🚴</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🥗</div>

                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🍎</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🧩</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🖋️</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🎹</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🎬</div>

                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🎯</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🛌</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">📅</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">🧹</div>
                    <div class="cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-pink-200">💻</div>
                </div>

                <input type="hidden" name="icon" id="selectedIcon">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="closeModalBtn2" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Hủy</button>

                <button type="submit"
                    class="px-5 py-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold shadow-lg hover:from-purple-600 hover:to-pink-600">
                    Tạo
                </button>
            </div>
        </form>
    </div>
</div>


<!-- POPUP SỬA THÓI QUEN -->
<div id="editHabitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-11/12 md:w-1/2 p-6 relative">

        <div class="mb-4 p-4 rounded-t-2xl bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold text-xl flex justify-between items-center">
            Sửa Thói Quen
            <button onclick="closeEditModal()" class="text-white text-2xl">&times;</button>
        </div>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="update_user_habit" value="1">
            <input type="hidden" name="habit_id" id="edit_habit_id">

            <div>
                <label class="block font-medium mb-1">Tên Thói Quen</label>
                <input id="edit_habit_name" name="habit_name" class="w-full border px-3 py-2 rounded-lg" required>
            </div>

            <div>
                <label class="block font-medium mb-1">Mô tả</label>
                <textarea id="edit_description" name="description"
                          class="w-full border px-3 py-2 rounded-lg" required></textarea>
            </div>

            <div>
                <label class="block font-medium mb-1">Icon</label>
                <input id="edit_icon" name="icon" class="w-full border px-3 py-2 rounded-lg" required>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-lg bg-gray-200">Hủy</button>
                <button type="submit" class="px-5 py-2 rounded-full bg-blue-500 text-white font-semibold">Cập nhật</button>
            </div>
        </form>

    </div>
</div>
<style>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to   { opacity: 1; transform: scale(1); }
}
.animate-fadeIn { animation: fadeIn 0.25s ease-out; }
</style>

<!-- FOOTER -->
<footer class="mt-10 bg-gradient-to-r from-purple-600 to-pink-500 text-white py-10 px-8 rounded-t-3xl">

  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10">

    <!-- Logo + mô tả -->
    <div>
      <div class="flex items-center gap-3 mb-3">
        <img src="assets/logo_habitu.png" width="40" class="rounded-full" />
        <h2 class="text-xl font-bold">Habitu</h2>
      </div>
      <p class="text-sm leading-relaxed">
        Xây dựng thói quen lành mạnh cùng Habitu! 🐱✨
      </p>

      <!-- Social icons -->
      <div class="flex gap-4 mt-4 text-xl">
        <a href="#" class="hover:text-yellow-300"><i class="fab fa-facebook"></i></a>
        <a href="#" class="hover:text-yellow-300"><i class="fab fa-twitter"></i></a>
        <a href="#" class="hover:text-yellow-300"><i class="fab fa-instagram"></i></a>
        <a href="#" class="hover:text-yellow-300"><i class="fab fa-youtube"></i></a>
      </div>
    </div>

    <!-- Liên kết nhanh -->
    <div>
      <h3 class="text-lg font-semibold mb-3">Liên Kết Nhanh</h3>
      <ul class="space-y-2 text-sm">
        <li><a href="dashboard.php" class="hover:text-yellow-300">Trang Chủ</a></li>
        <li><a href="journal.php" class="hover:text-yellow-300">Nhật Ký</a></li>
        <li><a href="community.php" class="hover:text-yellow-300">Cộng Đồng</a></li>
        <li><a href="thongke.php" class="hover:text-yellow-300">Thống Kê</a></li>
      </ul>
    </div>

    <!-- Tài nguyên -->
    <div>
      <h3 class="text-lg font-semibold mb-3">Tài Nguyên</h3>
      <ul class="space-y-2 text-sm">
        <li><a href="index.php" class="hover:text-yellow-300">Hướng Dẫn Sử Dụng</a></li>
        <li><a href="#" class="hover:text-yellow-300">Blog</a></li>
        <li><a href="#" class="hover:text-yellow-300">Câu Hỏi Thường Gặp</a></li>
        <li><a href="support.php" class="hover:text-yellow-300">Hỗ Trợ</a></li>
      </ul>
    </div>

    <!-- Liên hệ -->
    <div>
      <h3 class="text-lg font-semibold mb-3">Liên Hệ</h3>

      <p class="text-sm flex items-center gap-2">
        <i class="fas fa-envelope"></i> support@habitu.com
      </p>

      <p class="text-sm mt-3">Giờ làm việc:</p>
      <p class="text-sm">T2 - T6: 9:00 - 18:00</p>
    </div>

  </div>

  <!-- Dòng cuối -->
  <div class="text-center text-xs mt-10 opacity-80">
    © 2025 Habitu. Tất cả quyền được bảo lưu. |
    <a href="#" class="hover:text-yellow-300">Chính Sách Bảo Mật</a> • 
    <a href="#" class="hover:text-yellow-300">Điều Khoản Sử Dụng</a>
    <br>
    <div class="mt-2 flex justify-center items-center gap-1">
      Made with ❤️ by TMeo
    </div>
  </div>

</footer>

<script>
const createBtn = document.getElementById('addHabitBtn');
const modal = document.getElementById('createHabitModal');
const closeBtn = document.getElementById('closeModalBtn');
const closeBtn2 = document.getElementById('closeModalBtn2');

// Mở / đóng modal
createBtn.addEventListener('click', () => modal.classList.remove('hidden'));
closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
closeBtn2.addEventListener('click', () => modal.classList.add('hidden'));

// Icon picker
const iconInput = document.getElementById('iconInput');
const iconGrid = document.getElementById('iconGrid');
const selectedIcon = document.getElementById('selectedIcon');

iconInput.addEventListener('click', () => {
    iconGrid.classList.toggle('hidden');
});

// Khi chọn icon
iconGrid.querySelectorAll('div').forEach(div => {
    div.addEventListener('click', () => {
        selectedIcon.value = div.textContent;
        iconInput.value = div.textContent;
        iconGrid.classList.add('hidden');

        iconGrid.querySelectorAll('div').forEach(d => d.classList.remove('bg-pink-200'));
        div.classList.add('bg-pink-200');
    });
});

// Click ra ngoài để đóng
document.addEventListener('click', function(e){
    if (!iconInput.contains(e.target) && !iconGrid.contains(e.target)){
        iconGrid.classList.add('hidden');
    }
});


// Hàm mở popup Sửa thói quen
function openEditHabit(habitId, habitName, description, icon) {
    // Đổ dữ liệu vào form
    document.getElementById("edit_habit_id").value = habitId;
    document.getElementById("edit_habit_name").value = habitName;
    document.getElementById("edit_description").value = description;
    document.getElementById("edit_icon").value = icon;

    // Hiển thị popup
    document.getElementById("editHabitModal").classList.remove("hidden");
}

// Hàm đóng popup
function closeEditModal() {
    document.getElementById("editHabitModal").classList.add("hidden");
}

// Lấy tất cả checkbox thói quen
const allCheckboxes = document.querySelectorAll('.habit-checkbox');
const completedEl = document.getElementById('completedToday');
const percentEl = document.getElementById('completedPercent');
const totalStreakEl = document.getElementById('totalStreak');
const congratsMsg = document.getElementById('congratsMessage');
const audio = document.getElementById('celebrationSound');

allCheckboxes.forEach(cb => {
  cb.addEventListener('change', function() {
    const habitId = this.dataset.habitId;
    const completed = this.checked ? 'done' : 'missed';

    // 1️⃣ Cập nhật tạm thời trên client
    const completedCount = Array.from(allCheckboxes).filter(c => c.checked).length;
    const totalCount = allCheckboxes.length;
    completedEl.textContent = completedCount;
    percentEl.textContent = totalCount ? Math.round(completedCount / totalCount * 100) : 0;

    // Hiển thị/ẩn thông báo 100%
    if(completedCount === totalCount){
      if(congratsMsg){
        congratsMsg.style.display = 'block';
        audio.currentTime = 0;
        audio.play();
        setTimeout(() => { congratsMsg.style.display = 'none'; }, 3000);
      }
    } else {
      if(congratsMsg) congratsMsg.style.display = 'none';
    }

    // 2️⃣ Gửi request cập nhật lên server
    fetch('update_habit_log.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({habitId, completed})
    })
    .then(res => res.json())
    .then(data => {
      if(data.success){
        // Cập nhật streak của thói quen
        const streakEl = this.closest('.habit-item').querySelector('.streak');
        if(streakEl){
          streakEl.textContent = data.current_streak + ' ngày';
          streakEl.classList.add('streak-updated');
          setTimeout(()=> streakEl.classList.remove('streak-updated'), 1000);
        }

        // Cập nhật tổng chuỗi ngày
        if(totalStreakEl) totalStreakEl.textContent = data.total_streak + ' ngày';
      }
    })
    .catch(err => console.error('Error updating habit log:', err));
  });
});




</script>

<script src="./assets/js/dashboard.js"></script>

</body>

</html>
