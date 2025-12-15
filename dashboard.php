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

// Lấy mục tiêu sức khoẻ của user
$stmt = $pdo->prepare("SELECT health_goal FROM users WHERE user_id=?");
$stmt->execute([$user_id]);
$health_goal = $stmt->fetchColumn();


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
  $name = $_POST['habit_name'];
  $desc = $_POST['description'];
  $icon = $_POST['icon'];

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
$completed_today = count(array_filter($habit_logs, fn($c) => $c == 'done'));

/*==================== 3. Lấy tổng chuỗi ngày ====================*/
// Lấy total_streak từ bảng users
$stmt = $pdo->prepare("SELECT total_streak FROM users WHERE user_id=?");
$stmt->execute([$user_id]);
$total_streak = (int) $stmt->fetchColumn();

/*==================== 4. Lấy streak từng habit ====================*/
// Chuẩn bị mảng streak của từng habit để hiển thị
$habit_streaks = [];
foreach ($habits as $hb) {
  $habit_id = $hb['habit_id'];
  $stmt = $pdo->prepare("SELECT current_streak FROM habit WHERE habit_id=? AND user_id=?");
  $stmt->execute([$habit_id, $user_id]);
  $habit_streaks[$habit_id] = (int) $stmt->fetchColumn();
}

/*==================== 5. THÔNG ĐIỆP ĐỘNG LỰC THEO TIẾN ĐỘ ====================*/
$progress_percent = $total_habits > 0
  ? round($completed_today / $total_habits * 100)
  : 0;

if ($progress_percent == 100) {
  $motivation_msg = "🔥 Tuyệt vời! Hôm nay bạn đã tiến rất gần mục tiêu!";
} elseif ($progress_percent >= 50) {
  $motivation_msg = "✨ Bạn đang đi đúng hướng, cố thêm chút nữa nhé!";
} elseif ($progress_percent > 0) {
  $motivation_msg = "🌱 Mỗi bước nhỏ đều có giá trị!";
} else {
  $motivation_msg = "🚀 Hôm nay là một khởi đầu mới cho mục tiêu của bạn!";
}

?>


<!DOCTYPE html>
<html lang="vi">
<style>
  /* Giới hạn dòng chữ */
  .line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
  }

  .line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }
</style>

<body style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0)" ;>

  <!-- NAV -->
  <?php include "navbar.php"; ?>

  <!-- Header -->
  <div class="mb-6 text-center mt-10">

    <h1 class="text-2xl font-bold text-white leading-loose">
      Chào Mừng Bạn Đến Với Habitu! 🐱
    </h1>

    <p class="text-sm text-white leading-relaxed">
      Hãy tiếp tục xây dựng thói quen lành mạnh của bạn ngay thôi nào! ✨
    </p>
  </div>

 <?php if (!empty($health_goal)): ?>
  <div class="fixed right-32 top-28 z-40">

    <div class="w-35 h-35 rounded-full
           bg-gradient-to-br from-yellow-100 to-yellow-200
           shadow-xl flex flex-col items-center justify-center text-center
           p-4 animate-float
           hover:scale-105 transition-all duration-300">

      <div class="text-3xl mb-1">🎯</div>

      <h3 class="font-semibold text-gray-800 text-sm">
        Mục tiêu sức khoẻ
      </h3>

      <p class="text-gray-700 text-xs mt-1 italic line-clamp-3">
        “<?= htmlspecialchars($health_goal) ?>”
      </p>

      <span class="text-[11px] text-green-600 font-semibold mt-2">
        Cố lên! 💪
      </span>

    </div>

  </div>
<?php endif; ?>


<div
  id="motivationBox"
  class="motivation-box bg-soft text-white shadow-md
         fixed left-6 top-[18%] -translate-y-1/2
         transition-all duration-500
         flex items-start gap-3
         note-paper z-40">
  <!-- Mèo -->
  <img
    id="catAvatar"
    src="assets/cat_sad.png"
    alt="Cat"
    class="w-14 h-14 rounded-full transition-transform duration-500"
  >

  <!-- Lời nói -->
  <div class="relative bg-white/90 text-gray-800 px-3 py-2 rounded-lg shadow
              speech-bubble max-w-[200px]">
    <p id="motivationText" class="text-sm font-medium leading-relaxed">
      <?= $motivation_msg ?>
    </p>
  </div>

</div>
  <!-- Stats -->
  <section class="stats container mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 
         gap-6 px-4 mb-12">

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
          (<span id="completedPercent"><?= $total_habits ? round($completed_today / $total_habits * 100) : 0 ?></span>%)
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

  <!-- Motivation -->


  <!-- Habits -->
  <section class="habits-section px-6 mt-8">
    <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
      <h3 class="text-lg font-semibold text-gray-800">Thói Quen Của Bạn</h3>
      <button id="addHabitBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
        + Thêm Thói Quen
      </button>
    </div>

    <div id="habitList" class="habit-list grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php foreach ($habits as $hb): ?>
        <div class="habit-item bg-white p-4 rounded-xl shadow-md flex flex-col gap-3 relative h-full">

          <!-- Checkbox -->
          <div class="flex items-center justify-between">
            <input type="checkbox" class="habit-checkbox w-5 h-5" data-habit-id="<?= $hb['habit_id'] ?>"
              <?= isset($habit_logs[$hb['habit_id']]) && $habit_logs[$hb['habit_id']] === 'done' ? 'checked' : '' ?>>

            <div class="text-3xl"><?= htmlspecialchars($hb['icon']) ?></div>
          </div>

          <!-- Tên + mô tả -->
          <div>
            <h4 class="font-semibold text-gray-800 line-clamp-1"><?= htmlspecialchars($hb['habit_name']) ?></h4>
            <p class="text-gray-500 text-sm line-clamp-2"><?= htmlspecialchars($hb['description']) ?></p>
          </div>

          <!-- Chuỗi streak -->
          <div class="flex items-center justify-between mt-auto">
            <div class="streak text-orange-400 font-semibold text-sm"></div>

            <div class="flex gap-2">
              <button
                onclick="openEditHabit('<?= $hb['habit_id'] ?>', '<?= htmlspecialchars($hb['habit_name']) ?>', '<?= htmlspecialchars($hb['description']) ?>', '<?= htmlspecialchars($hb['icon']) ?>')"
                class="text-blue-600 hover:text-blue-800">
                ✏️
              </button>

              <a href="dashboard.php?delete_user_habit=<?= $hb['habit_id'] ?>"
                onclick="return confirm('Xóa thói quen này?')" class="text-red-600 hover:text-red-800">
                🗑️
              </a>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- POPUP THÊM THÓI QUEN -->

  <div id="createHabitModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-11/12 md:w-1/2 p-6 relative">

      <!-- Header -->
      <div
        class="mb-4 p-4 rounded-t-2xl bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold text-xl flex justify-between items-center">
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
            class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-pink-400" required>
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
            class="w-full border border-gray-300 px-3 py-2 rounded-lg cursor-pointer focus:ring-2 focus:ring-pink-400"
            required>

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
          <button type="button" id="closeModalBtn2"
            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Hủy</button>

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

      <div
        class="mb-4 p-4 rounded-t-2xl bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold text-xl flex justify-between items-center">
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
          <textarea id="edit_description" name="description" class="w-full border px-3 py-2 rounded-lg"
            required></textarea>
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
    /* ======= RESPONSIVE HABITU DASHBOARD ======= */

    /* --- Stats responsive --- */
    @media (max-width: 768px) {
      .stats {
        grid-template-columns: 1fr !important;
      }

      .stat-box {
        flex-direction: row;
        padding: 15px;
      }
    }

    /* --- Habit item responsive --- */
    @media (max-width: 640px) {
      .habit-item {
        flex-wrap: wrap;
        padding: 16px;
      }

      .habit-item .text-3xl {
        font-size: 26px;
      }

      .habit-item .streak {
        width: 100%;
        margin: 10px 0 0;
        text-align: right;
      }

      .habit-item .flex.gap-2 {
        margin-left: auto;
      }
    }

    /* --- Popup mobile full width --- */
    @media (max-width: 640px) {

      #createHabitModal .md\:w-1\/2,
      #editHabitModal .md\:w-1\/2 {
        width: 90% !important;
        margin: 0 10px;
      }
    }

    /* --- Icon grid fix for mobile --- */
    @media (max-width: 640px) {
      #iconGrid {
        grid-template-columns: repeat(4, 1fr);
        max-height: 200px;
      }
    }

    /* --- Header --- */
    @media (max-width: 640px) {
      h1.text-2xl {
        font-size: 20px;
      }

      p.text-sm {
        font-size: 14px;
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.95);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .animate-fadeIn {
      animation: fadeIn 0.25s ease-out;
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(-50%) translateX(0);
      }

      50% {
        transform: translateY(-55%) translateX(2px);
      }
    }

    .animate-float {
      animation: float 4s ease-in-out infinite;
    }

    /* bong bóng thoại */
.speech-bubble::before {
  content: "";
  position: absolute;
  left: -8px;
  top: 50%;
  transform: translateY(-50%);
  border-width: 8px;
  border-style: solid;
  border-color: transparent white transparent transparent;
}

/* mèo chuyển động nhẹ */
@keyframes catFloat {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-4px);
  }
}

.animate-cat {
  animation: catFloat 3s ease-in-out infinite;
}
   

/* note giấy */
.note-paper {
  padding: 14px;
  border-radius: 14px;
  background: linear-gradient(135deg, #fffbe6, #fff3c4);
  box-shadow:
    0 10px 25px rgba(0, 0, 0, 0.15),
    inset 0 0 0 1px rgba(255, 255, 255, 0.6);

  animation: noteFloat 4s ease-in-out infinite;
}

/* note nhúc nhích nhẹ */
@keyframes noteFloat {
  0%, 100% {
    transform: translateY(-50%) translateX(0);
  }
  50% {
    transform: translateY(-50%) translateX(4px);
  }
}

/* bong bóng thoại */
.speech-bubble::before {
  content: "";
  position: absolute;
  left: -8px;
  top: 16px;
  border-width: 8px;
  border-style: solid;
  border-color: transparent white transparent transparent;
}

  </style>

  <!-- FOOTER -->
  <?php include "footer.php"; ?>

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

  iconGrid.querySelectorAll('div').forEach(div => {
    div.addEventListener('click', () => {
      selectedIcon.value = div.textContent;
      iconInput.value = div.textContent;
      iconGrid.classList.add('hidden');

      iconGrid.querySelectorAll('div').forEach(d => d.classList.remove('bg-pink-200'));
      div.classList.add('bg-pink-200');
    });
  });

  document.addEventListener('click', function (e) {
    if (!iconInput.contains(e.target) && !iconGrid.contains(e.target)) {
      iconGrid.classList.add('hidden');
    }
  });

  // ===== Edit habit =====
  function openEditHabit(habitId, habitName, description, icon) {
    document.getElementById("edit_habit_id").value = habitId;
    document.getElementById("edit_habit_name").value = habitName;
    document.getElementById("edit_description").value = description;
    document.getElementById("edit_icon").value = icon;
    document.getElementById("editHabitModal").classList.remove("hidden");
  }

  function closeEditModal() {
    document.getElementById("editHabitModal").classList.add("hidden");
  }

  // ===== Habit logic =====
  const allCheckboxes = document.querySelectorAll('.habit-checkbox');
  const completedEl = document.getElementById('completedToday');
  const percentEl = document.getElementById('completedPercent');
  const totalStreakEl = document.getElementById('totalStreak');
  const congratsMsg = document.getElementById('congratsMessage');
  const audio = document.getElementById('celebrationSound');

  const motivationBox = document.getElementById('motivationBox');
  const motivationText = document.getElementById('motivationText');

  // ===== Mèo nói chuyện – UI =====
  function updateMotivationUI() {
  const motivationBox = document.getElementById('motivationBox');
  const motivationText = document.getElementById('motivationText');
  const catAvatar = document.getElementById('catAvatar');

  const completedCount = Array.from(allCheckboxes).filter(c => c.checked).length;
  const totalCount = allCheckboxes.length;
  const percent = totalCount
    ? Math.round((completedCount / totalCount) * 100)
    : 0;

  completedEl.textContent = completedCount;
  percentEl.textContent = percent;

  // 🐱 Biểu cảm mèo theo tiến độ
  if (percent === 100 && totalCount > 0) {
    motivationBox.classList.remove('bg-soft');
    motivationBox.classList.add('bg-fire');

    catAvatar.src = "assets/cat_fun.png";
    catAvatar.classList.add('scale-110');

    motivationText.textContent =
      "😻 Meowww~ Đỉnh quá! Hôm nay bạn hoàn thành tất cả rồi đó!";
  }
  else if (percent >= 50) {
    motivationBox.classList.remove('bg-fire');
    motivationBox.classList.add('bg-soft');

    catAvatar.src = "assets/cat_ok.png";
    catAvatar.classList.remove('scale-110');

    motivationText.textContent =
      "😺 Tốt lắm nè~ Mình thấy bạn đang cố gắng đó!";
  }
  else {
    motivationBox.classList.remove('bg-fire');
    motivationBox.classList.add('bg-soft');

    catAvatar.src = "assets/cat_sad.png";
    catAvatar.classList.remove('scale-110');

    motivationText.textContent =
      "😿 Buồn ghê~ Bạn chả cố gắng tí nào!";
  }
}

  // ===== Checkbox change =====
  allCheckboxes.forEach(cb => {
    cb.addEventListener('change', function () {
      const habitId = this.dataset.habitId;
      const completed = this.checked ? 'done' : 'missed';

      updateMotivationUI();

      const completedCount = Array.from(allCheckboxes).filter(c => c.checked).length;
      const totalCount = allCheckboxes.length;

      if (completedCount === totalCount && totalCount > 0 && congratsMsg) {
        congratsMsg.style.display = 'block';
        audio.currentTime = 0;
        audio.play();
        setTimeout(() => congratsMsg.style.display = 'none', 3000);
      } else if (congratsMsg) {
        congratsMsg.style.display = 'none';
      }

      fetch('update_habit_log.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ habitId, completed })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            const streakEl = this.closest('.habit-item')?.querySelector('.streak');
            if (streakEl) {
              streakEl.textContent = data.current_streak + ' ngày';
              streakEl.classList.add('streak-updated');
              setTimeout(() => streakEl.classList.remove('streak-updated'), 1000);
            }
            if (totalStreakEl) totalStreakEl.textContent = data.total_streak + ' ngày';
          }
        })
        .catch(err => console.error('Error updating habit log:', err));
    });
  });

  // ===== Init when load =====
  document.addEventListener('DOMContentLoaded', () => {
    updateMotivationUI();
  });
</script>


  <script src="./assets/js/dashboard.js"></script>

</body>

</html>