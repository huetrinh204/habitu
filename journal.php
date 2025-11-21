<?php
session_start();

// Nếu chưa đăng nhập → chuyển về trang đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location: dangnhap.php");
    exit();
}

// Lấy username từ session
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="vi">


<body class="bg-gradient-to-br from-cyan-300 to-teal-400 min-h-screen">


<!-- NAV -->
<?php include "navbar.php"; ?>

<!-- HEADER -->
<header class="text-center py-6" >
  <h2 style="color: aliceblue;" class="text-2xl font-semibold text-gray-800">Nhật ký của bạn 📘✨</h2>
  <p style="color: aliceblue;" class="text-gray-600">Ghi lại cảm xúc và suy nghĩ mỗi ngày.</p>
</header>

<!-- MAIN CONTENT -->
<section class="container mx-auto mt-8 px-6">

  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold text-gray-700">Danh sách nhật ký</h3>

    <button id="openJournalPopup"
      class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md shadow">
      + Viết Nhật Ký
    </button>
  </div>

  <!-- Khu vực danh sách nhật ký -->
  <div id="journalList" class="space-y-4">
    <!-- Nhật ký hiển thị ở đây khi bạn kết nối PHP -->
  </div>

</section>

<!-- POPUP VIẾT NHẬT KÝ -->
<div id="journalPopup"
     class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center hidden">

  <div class="bg-white w-[550px] p-6 rounded-xl shadow-xl animate-fadeIn">

    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-semibold">Viết Nhật Ký Mới</h3>
      <button onclick="closeJournalPopup()" class="text-gray-500 text-xl hover:text-black">×</button>
    </div>

    <div class="space-y-4">

      <!-- Ngày -->
      <div>
        <label class="font-semibold">Ngày</label>
        <input id="journalDate" type="date"
               class="w-full border px-3 py-2 rounded-md mt-1">
      </div>

      <!-- Tiêu đề -->
      <div>
        <label class="font-semibold">Tiêu đề</label>
        <input id="journalTitle" type="text" placeholder="Nhập tiêu đề..."
               class="w-full border px-3 py-2 rounded-md mt-1">
      </div>

      <!-- Nội dung -->
      <div>
        <label class="font-semibold">Nội dung</label>
        <textarea id="journalContent" rows="4"
                  placeholder="Chia sẻ cảm xúc và suy nghĩ của bạn..."
                  class="w-full border px-3 py-2 rounded-md mt-1"></textarea>
      </div>

      <!-- Icon cảm xúc -->
      <div>
        <label class="font-semibold">Cảm xúc hôm nay</label>

        <div id="emotionList" class="flex gap-4 text-2xl mt-2 cursor-pointer select-none">
          <span>😊</span>
          <span>😄</span>
          <span>😐</span>
          <span>😔</span>
          <span>😢</span>
          <span>😡</span>
          <span>💪</span>
          <span>✨</span>
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button onclick="closeJournalPopup()"
                class="px-4 py-2 border rounded-md hover:bg-gray-100">Hủy</button>

        <button id="saveJournal"
                class="px-5 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Lưu</button>
      </div>

    </div>

  </div>
</div>

<!-- SCRIPT -->
<script>
  const popup = document.getElementById("journalPopup");
  document.getElementById("openJournalPopup").onclick = () => popup.classList.remove("hidden");
  function closeJournalPopup() { popup.classList.add("hidden"); }

  // Chọn icon cảm xúc
  const emotionList = document.getElementById("emotionList");
  let selectedEmotion = "";

  emotionList.addEventListener("click", (e) => {
    if (e.target.tagName === "SPAN") {
      selectedEmotion = e.target.textContent;

      [...emotionList.children].forEach(el => el.classList.remove("ring-2", "ring-purple-500"));

      e.target.classList.add("ring-2", "ring-purple-500");
    }
  });
</script>

</body>
</html>
