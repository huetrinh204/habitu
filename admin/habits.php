<?php
include "../config.php";// kết nối DB

// gán user_id cho thói quen mẫu
$user_id = 17; // hoặc user_id System có sẵn trong bảng user

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_habit'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];

    $stmt = $pdo->prepare("
        INSERT INTO habit (habit_name, description, icon, status, created_hb, user_id) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $description, $icon, 'Mẫu', date("Y-m-d H:i:s"), $user_id]);

    // Chuyển hướng để tránh resubmit khi reload
    header("Location: habits.php");
    exit;
}

// Xoá
if(isset($_GET['delete_id'])){
    $id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM habit WHERE id=? LIMIT 1");
    $stmt->execute([$id]);
    exit;
}

// Cập nhật từ popup edit
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['habit_id'])){
    $id = $_POST['habit_id'];
    $name = $_POST['habit_name'];
    $desc = $_POST['description'];
    $icon = $_POST['icon'];

    $stmt = $pdo->prepare("UPDATE habit SET habit_name=?, description=?, icon=? WHERE id=?");
    $stmt->execute([$name, $desc, $icon, $id]);
    exit;
}

//Tìm kiếm thói quen
 $search = '';
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = "%".$_GET['search']."%";
    $stmt = $pdo->prepare("SELECT * FROM habit WHERE habit_name LIKE ? ORDER BY created_hb DESC");
    $stmt->execute([$search]);
} else {
    $stmt = $pdo->query("SELECT * FROM habit ORDER BY created_hb DESC");
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Quản lý Thói Quen - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-tr from-cyan-300 to-sky-400 min-h-screen">

<!-- NAV -->
<?php include "navbar.php"; ?>

<?php
// Tổng thói quen (tất cả)
$stmtTotal = $pdo->query("SELECT COUNT(*) AS total_habits FROM habit");
$totalHabits = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_habits'];

// Thói quen mẫu (status = 'Mẫu')
$stmtSample = $pdo->query("SELECT COUNT(*) AS sample_habits FROM habit WHERE status='Mẫu'");
$sampleHabits = $stmtSample->fetch(PDO::FETCH_ASSOC)['sample_habits'];

// Tổng người dùng
$stmtUsers = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)['total_users'];



//$stmtCompleted = $pdo->query("SELECT COUNT(*) AS total_completed FROM habit_completed");
//$totalCompleted = $stmtCompleted->fetch(PDO::FETCH_ASSOC)['total_completed'] ?? 0;
?>

<!-- Tổng quan -->
<div class="px-10 py-5">
    <h1 class="text-3xl font-bold" style="color:#ffffff; text-shadow:2px 2px 6px rgba(0,0,0,0.5)">Quản Lý Thói Quen</h1>
    <p class="text-gray-700 mb-6">Quản lý thói quen của người dùng và tạo thói quen mẫu</p>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <p class="text-gray-500">Tổng thói quen</p>
            <h2 class="text-3xl font-bold text-blue-600"><?php echo $totalHabits; ?></h2>
        </div>
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <p class="text-gray-500">Thói quen mẫu</p>
            <h2 class="text-3xl font-bold text-green-600"><?php echo $sampleHabits; ?></h2>
        </div>
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <p class="text-gray-500">Tổng người dùng</p>
            <h2 class="text-3xl font-bold text-orange-500"><?php echo $totalUsers; ?></h2>
        </div>
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <p class="text-gray-500">Tổng hoàn thành</p>
            <h2 class="text-3xl font-bold text-red-600">Updating</h2>
        </div>
    </div>


<!-- Search + Tạo Thói Quen Mẫu -->
<div class="flex flex-wrap gap-4 mb-6 items-center justify-between">
    <!-- Form tìm kiếm -->
   <form method="GET" class="flex gap-2 mb-6 items-center w-full">
    <input type="text" name="search" 
       placeholder="🔍 Tìm kiếm thói quen..." 
       class="border border-gray-300 px-4 py-2 rounded-l-lg flex-1 focus:outline-none"
       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
           
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 transition">
        <i class="ri-search-line"></i> Tìm
    </button>

    <a href="habits.php" class="bg-gray-200 px-4 py-2 rounded ml-2 hover:bg-gray-300 transition">
        Tất cả
    </a>

   <button id="createHabitBtn" type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded ml-auto font-bold">
    + Tạo Thói Quen Mẫu
</button>
</form>


</div>

    
    

<!-- Popup Form -->
<div id="createHabitModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-11/12 md:w-1/2 p-6 relative">
        <!-- Header -->
        <div class="mb-4 p-4 rounded-t-2xl bg-gradient-to-r from-blue-400 to-cyan-500 text-white font-bold text-xl flex justify-between items-center">
            Tạo Thói Quen Mẫu
            <button id="closeModalBtn" class="text-white text-2xl font-bold hover:text-gray-200">&times;</button>
        </div>

        <form method="POST" class="space-y-4">
            <!-- Tên thói quen -->
            <div>
                <label class="block font-medium mb-1" style="font-family: Arial, Helvetica, sans-serif">Tên Thói Quen</label>
                <input name="name" type="text" class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400" required>
            </div>

            <!-- Mô tả -->
            <div>
                <label class="block font-medium mb-1" style="font-family: Arial, Helvetica, sans-serif"">Mô tả</label>
                <textarea name="description" class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400" required></textarea>
            </div>

            <!-- Chọn Icon -->
            <div class="relative">
                <label class="block font-medium mb-1" style="font-family: Arial, Helvetica, sans-serif"">Chọn Icon</label>
                <input id="iconInput" type="text" readonly placeholder="Chọn icon..." 
                       class="w-full border border-gray-300 px-3 py-2 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-cyan-400" required>

                <!-- Lưới icon ẩn -->
                <div id="iconGrid" class="absolute top-full left-0 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 grid grid-cols-5 gap-2 hidden max-h-48 overflow-y-auto z-50">
                    <!-- 25 icon -->
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">💧</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🏃</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">📚</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🧘</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">😴</div>

                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🗣️</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">💰</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">📝</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎧</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎨</div>

                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">📖</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">⚽</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🏊</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🚴</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🥗</div>

                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🍎</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🧩</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🖋️</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎹</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎬</div>

                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎯</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🛌</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">📅</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🧹</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">💻</div>
                </div>

                <input type="hidden" name="icon" id="selectedIcon">
            </div>

            <!-- Nút Tạo -->
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="closeModalBtn2" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Hủy</button>
                <button type="submit" name="create_habit" class="px-5 py-2 rounded-full bg-gradient-to-r from-blue-400 to-cyan-500 text-white font-semibold shadow-lg hover:from-blue-500 hover:to-cyan-600 transition">Tạo</button>
            </div>
        </form>
    </div>
</div>

<script>
const createBtn = document.getElementById('createHabitBtn');
const modal = document.getElementById('createHabitModal');
const closeBtn = document.getElementById('closeModalBtn');
const closeBtn2 = document.getElementById('closeModalBtn2');

// Mở / đóng popup
createBtn.addEventListener('click', () => modal.classList.remove('hidden'));
closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
closeBtn2.addEventListener('click', () => modal.classList.add('hidden'));

// Chọn icon
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

        // Highlight icon đã chọn
        iconGrid.querySelectorAll('div').forEach(d => d.classList.remove('bg-blue-200'));
        div.classList.add('bg-blue-200');
    });
});

// Click ngoài để ẩn grid
document.addEventListener('click', function(e){
    if (!iconInput.contains(e.target) && !iconGrid.contains(e.target)){
        iconGrid.classList.add('hidden');
    }
});
</script>

<!-- Popup Edit Habit -->
<div id="editHabitModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-11/12 md:w-1/2 p-6 relative">
        <!-- Header -->
        <div class="mb-4 p-4 rounded-t-2xl bg-gradient-to-r from-blue-400 to-cyan-500 text-white font-bold text-xl flex justify-between items-center">
            Chỉnh sửa Thói Quen
            <button id="closeEditModalBtn" class="text-white text-2xl font-bold hover:text-gray-200">&times;</button>
        </div>

        <form id="editHabitForm" method="POST" action="update_habit.php" class="space-y-4">
            <input type="hidden" name="habit_id" id="edit_habit_id">

            <!-- Tên thói quen -->
            <div>
                <label class="block font-medium mb-1">Tên Thói Quen</label>
                <input name="habit_name" id="edit_habit_name" type="text" class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400" required>
            </div>

            <!-- Mô tả -->
            <div>
                <label class="block font-medium mb-1">Mô tả</label>
                <textarea name="description" id="edit_description" class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400" required></textarea>
            </div>

            <!-- Chọn Icon -->
            <div class="relative">
                <label class="block font-medium mb-1">Chọn Icon</label>
                <input id="edit_iconInput" type="text" readonly placeholder="Chọn icon..." 
                       class="w-full border border-gray-300 px-3 py-2 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-cyan-400" required>

                <div id="edit_iconGrid" class="absolute top-full left-0 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-2 grid grid-cols-5 gap-2 hidden max-h-48 overflow-y-auto z-50">
                    <!-- Copy 25 icon từ form tạo thói quen -->
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">💧</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🏃</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">📚</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🧘</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">😴</div>

                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🗣️</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">💰</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">📝</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎧</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎨</div>

                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">📖</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">⚽</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🏊</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🚴</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🥗</div>

                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🍎</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🧩</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🖋️</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎹</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎬</div>

                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🎯</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🛌</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">📅</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">🧹</div>
                    <div class="cursor-pointer text-2xl flex items-center justify-center p-2 rounded-lg hover:bg-gradient-to-r from-blue-200 to-cyan-200 transition">💻</div>
                </div>

                <input type="hidden" name="icon" id="edit_selectedIcon">
            </div>

            <!-- Nút cập nhật -->
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="closeEditModalBtn2" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Hủy</button>
                <button type="submit" class="px-5 py-2 rounded-full bg-gradient-to-r from-blue-400 to-cyan-500 text-white font-semibold shadow-lg hover:from-blue-500 hover:to-cyan-600 transition">Cập nhật</button>
            </div>
        </form>
    </div>
</div>




<!-- Habits Table -->
   <div class="bg-white shadow rounded-lg p-5 overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b text-gray-700 font-bold">
                <th>Thói quen</th>
                <th>Mô tả</th>
                <th>Người tạo</th>
                <th>Người dùng</th>
                <th>Hoàn thành</th>
                <th>Streak TB</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
      <tbody>
<?php
$habits = $stmt->fetchAll(PDO::FETCH_ASSOC); // lấy tất cả kết quả

if(count($habits) > 0){
    foreach($habits as $row){
        echo "<tr class='border-b hover:bg-gray-50'>";
        echo "<td class='flex items-center gap-2 py-2'>
                <div class='w-8 h-8 bg-blue-400 text-white rounded-full flex items-center justify-center font-bold' 
                     style='font-family: \"Segoe UI Emoji\", \"Apple Color Emoji\", \"Noto Color Emoji\", sans-serif;'>{$row['icon']}</div>
                {$row['habit_name']}
              </td>";
        echo "<td>{$row['description']}</td>";
        echo "<td>";
        if ($row['user_id'] == 17) {
            echo "System";
        } else {
            $stmtUser = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
            $stmtUser->execute([$row['user_id']]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
            echo $user ? $user['username'] : "Không tìm thấy";
        }
        echo "</td>";
        echo "<td>---</td>";
        echo "<td>---</td>";
        echo "<td>---</td>";
        echo "<td class='text-center text-lg'>
                <button class='edit-btn text-blue-500 mx-1' 
                        data-id='{$row['habit_id']}' 
                        data-name='".htmlspecialchars($row['habit_name'], ENT_QUOTES)."' 
                        data-desc='".htmlspecialchars($row['description'], ENT_QUOTES)."' 
                        data-icon='".htmlspecialchars($row['icon'], ENT_QUOTES)."'>
                    <i class='ri-edit-2-line cursor-pointer'></i>
                </button>
                <button class='delete-btn text-red-500 mx-1' data-id='{$row['habit_id']}'>
                    <i class='ri-delete-bin-6-line cursor-pointer'></i>
                </button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center py-4 text-gray-700 italic'>Oops! Hiện tại chưa có thói quen này 😅</td></tr>";

}
?>
</tbody>

    </table>

   </div>
</div>

<script>
// --- mở popup chỉnh sửa ---
const editHabitModal = document.getElementById('editHabitModal');
const closeEditBtn = document.getElementById('closeEditModalBtn');
const closeEditBtn2 = document.getElementById('closeEditModalBtn2');
const editForm = document.getElementById('editHabitForm');
const edit_iconInput = document.getElementById('edit_iconInput');
const edit_iconGrid = document.getElementById('edit_iconGrid');
const edit_selectedIcon = document.getElementById('edit_selectedIcon');

// Mở popup khi nhấn nút edit
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit_habit_id').value = btn.dataset.id;
        document.getElementById('edit_habit_name').value = btn.dataset.name;
        document.getElementById('edit_description').value = btn.dataset.desc;
        document.getElementById('edit_iconInput').value = btn.dataset.icon;
        document.getElementById('edit_selectedIcon').value = btn.dataset.icon;

        editHabitModal.classList.remove('hidden');
    });
});

// Đóng popup
closeEditBtn.addEventListener('click', () => editHabitModal.classList.add('hidden'));
closeEditBtn2.addEventListener('click', () => editHabitModal.classList.add('hidden'));

// Chọn icon
edit_iconInput.addEventListener('click', () => edit_iconGrid.classList.toggle('hidden'));

edit_iconGrid.querySelectorAll('div').forEach(div => {
    div.addEventListener('click', () => {
        edit_selectedIcon.value = div.textContent;
        edit_iconInput.value = div.textContent;
        edit_iconGrid.classList.add('hidden');

        // Highlight icon
        edit_iconGrid.querySelectorAll('div').forEach(d => d.classList.remove('bg-blue-200'));
        div.classList.add('bg-blue-200');
    });
});

// Click ngoài để ẩn grid
document.addEventListener('click', function(e){
    if (!edit_iconInput.contains(e.target) && !edit_iconGrid.contains(e.target)){
        edit_iconGrid.classList.add('hidden');
    }
});

// --- submit form AJAX để update ---
editForm.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(editForm);

    fetch('update_habit.php', {
        method: 'POST',
        body: formData
    }).then(res => res.text())
      .then(data => location.reload());
});

// --- nút xoá ---
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const habitId = btn.dataset.id;
        if(confirm("Bạn có chắc chắn muốn xoá thói quen này?")) {
            fetch('delete_habit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `habit_id=${habitId}`
            })
            .then(res => res.text())
            .then(data => {
                // Xoá dòng trong bảng ngay lập tức
                const row = btn.closest('tr');
                row.remove();
            });
        }
    });
});


//Tìm kiếm thói quen

</script>

</body>
</html>
