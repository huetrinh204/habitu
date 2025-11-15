<?php
include "../config.php";// kết nối DB

// gán user_id cho thói quen mẫu
$user_id = 2; // hoặc user_id System có sẵn trong bảng user

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

<div class="px-10 py-5">
    <h1 class="text-3xl font-bold" style="color:#ffffff; text-shadow:2px 2px 6px rgba(0,0,0,0.5)">Quản Lý Thói Quen</h1>
    <p class="text-gray-700 mb-6">Quản lý thói quen của người dùng và tạo thói quen mẫu</p>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <p class="text-gray-500">Tổng thói quen</p>
            <h2 class="text-3xl font-bold text-blue-600">8</h2>
        </div>
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <p class="text-gray-500">Thói quen mẫu</p>
            <h2 class="text-3xl font-bold text-green-600">5</h2>
        </div>
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <p class="text-gray-500">Tổng người dùng</p>
            <h2 class="text-3xl font-bold text-orange-500">16.955</h2>
        </div>
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <p class="text-gray-500">Tổng hoàn thành</p>
            <h2 class="text-3xl font-bold text-red-600">227.266</h2>
        </div>
    </div>

    <!-- Search + Filter -->
    <div class="flex flex-wrap gap-4 mb-6 items-center">
        <input type="text" placeholder="🔍 Tìm kiếm thói quen..."
               class="border border-gray-300 px-4 py-2 rounded-lg w-1/2 focus:outline-none">
        <button class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">Tất cả</button>
        <button class="bg-yellow-200 hover:bg-yellow-300 px-3 py-1 rounded">Mẫu</button>
        <button class="bg-green-200 hover:bg-green-300 px-3 py-1 rounded">Hoạt động</button>
       



    <!-- Nút Tạo Thói Quen Mẫu -->
<button id="createHabitBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded ml-auto">
    Tạo Thói Quen Mẫu
</button>
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


<!-- Habits Table -->
   <div class="bg-white shadow rounded-lg p-5 overflow-x-auto">
    <table class="w-full text-left">
        <thead>
            <tr class="border-b text-gray-700 font-bold">
                <th>Thói quen</th>
                <th>Trạng thái</th>
                <th>Người tạo</th>
                <th>Người dùng</th>
                <th>Hoàn thành</th>
                <th>Streak TB</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $stmt = $pdo->query("SELECT * FROM habit ORDER BY created_hb DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr class='border-b hover:bg-gray-50'>";
             echo "<td class='flex items-center gap-2 py-2'>
        <div class='w-8 h-8 bg-blue-400 text-white rounded-full flex items-center justify-center font-bold' style='font-family: \"Segoe UI Emoji\", \"Apple Color Emoji\", \"Noto Color Emoji\", sans-serif;'>{$row['icon']}</div>
        {$row['habit_name']}
      </td>";

echo "<td>{$row['status']}</td>";
echo "<td>";
if ($row['user_id'] == 1) {
    echo "System";
} else {
    echo "Người dùng"; // hoặc truy vấn tên người dùng nếu muốn
}
echo "</td>";
                echo "<td>---</td>";
                echo "<td>---</td>";
                echo "<td>---</td>";
                echo "<td class='text-center text-lg'>
                        <i class='ri-edit-2-line text-blue-500 cursor-pointer mx-1'></i>
                        <i class='ri-delete-bin-6-line text-red-500 cursor-pointer mx-1'></i>
                      </td>";
                echo "</tr>";
            }
        ?>
        </tbody>
    </table>

    </div>
</div>

</body>
</html>
