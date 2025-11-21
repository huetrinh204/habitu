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
<head>

  <style>
    /* Animation cho popup */
    .popup-enter {
      opacity: 0;
      transform: scale(0.95);
    }
    .popup-enter-active {
      opacity: 1;
      transform: scale(1);
      transition: all 0.25s ease-out;
    }
    .popup-exit {
      opacity: 1;
      transform: scale(1);
    }
    .popup-exit-active {
      opacity: 0;
      transform: scale(0.9);
      transition: all 0.2s ease-in;
    }
  </style>
</head>

<body class="bg-gradient-to-br from-cyan-300 to-teal-400 min-h-screen">

<?php include "navbar.php"; ?>

<!-- MAIN CONTENT -->
<section class="container mx-auto mt-10 px-4">

  <!-- MAIN CARD -->
  <div class="bg-white/90 backdrop-blur-sm p-8 rounded-3xl shadow-xl max-w-4xl mx-auto">

    <h2 class="text-xl font-semibold text-teal-700">Cộng Đồng Mèo Thói Quen</h2>
    <p class="text-gray-700 mb-4">
      Chia sẻ hành trình và động viên nhau xây dựng thói quen lành mạnh 🐾✨
    </p>

    <!-- FORM ĐĂNG BÀI -->
    <div class="mb-6">
      <textarea
        placeholder="Chia sẻ câu chuyện của bạn..."
        class="w-full h-32 border border-teal-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-teal-400 bg-gray-50">
      </textarea>

      <button class="mt-3 px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded-md flex items-center gap-2">
        <i class="fa-solid fa-paper-plane"></i> Đăng Bài
      </button>
    </div>

    <!-- BÀI VIẾT DEMO -->
    <div class="bg-white rounded-2xl shadow p-5 border cursor-pointer"
         onclick="openPostDetail()">

      <div class="flex gap-3">
        <img src="assets/icons/avatar1.png" class="w-12 h-12 rounded-full border">

        <div>
          <h4 class="font-semibold">Minh Anh</h4>
          <p class="text-gray-500 text-sm">2 giờ trước</p>
        </div>
      </div>

      <p class="mt-3 text-gray-800 leading-relaxed">
        Mình đã hoàn thành 30 ngày liên tục tập thể dục! Cảm giác thật tuyệt vời 💪✨
      </p>

      <div class="flex gap-6 mt-4 text-gray-600">
        <span class="flex items-center gap-1"><i class="fa-regular fa-heart"></i> 24</span>
        <span class="flex items-center gap-1"><i class="fa-regular fa-comment"></i> 1</span>
      </div>

    </div>

  </div>

</section>



<!-- POPUP XEM BÀI CHI TIẾT -->
<div id="postDetailPopup"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden justify-center items-center p-4 z-50">

  <div id="postDetailCard"
       class="bg-white w-full max-w-2xl rounded-2xl shadow-xl p-6 popup-enter">

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-xl font-semibold text-gray-800">Bài viết</h3>
      <button onclick="closePostDetail()" class="text-gray-500 text-2xl hover:text-black">×</button>
    </div>

    <!-- Author Info -->
    <div class="flex gap-3 items-center mb-4">
      <img id="detailAvatar" src="" class="w-12 h-12 rounded-full border">
      <div>
        <h4 id="detailName" class="font-semibold text-gray-800"></h4>
        <p id="detailTime" class="text-gray-500 text-sm"></p>
      </div>
    </div>

    <!-- Content -->
    <p id="detailContent" class="text-gray-800 leading-relaxed mb-4"></p>

    <!-- Reaction -->
    <div class="flex gap-6 text-gray-600 mb-6">
      <span class="flex items-center gap-1"><i class="fa-regular fa-heart"></i> <span id="detailLikes">0</span></span>
      <span class="flex items-center gap-1"><i class="fa-regular fa-comment"></i> <span id="detailCommentsCount">0</span></span>
    </div>

    <!-- Comment List -->
    <div id="commentList" class="space-y-3 max-h-56 overflow-y-auto pr-2"></div>

    <!-- Write Comment -->
    <div class="mt-4">
      <textarea id="commentInput"
                placeholder="Viết bình luận..."
                class="w-full border border-gray-300 rounded-lg p-3 h-20 focus:ring-2 focus:ring-teal-400"></textarea>

      <button onclick="addComment()"
        class="mt-2 px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded-md shadow">
        Gửi bình luận
      </button>
    </div>

  </div>
</div>



<!-- JAVASCRIPT -->
<script>
let selectedPost = null;

// POST DEMO (sau này thay bằng PHP)
const demoPost = {
  avatar: "assets/icons/avatar1.png",
  name: "Minh Anh",
  time: "2 giờ trước",
  content: "Mình đã hoàn thành 30 ngày liên tục tập thể dục! Cảm giác thật tuyệt vời 💪✨",
  likes: 24,
  comments: [
    { name: "Bảo Ngọc", text: "Tuyệt vời quá! Chúc mừng bạn 🎉" },
    { name: "Huy Hoàng", text: "Cố gắng duy trì nữa nha!" }
  ]
};


function openPostDetail(post = demoPost) {
  selectedPost = post;

  document.getElementById("detailAvatar").src = post.avatar;
  document.getElementById("detailName").textContent = post.name;
  document.getElementById("detailTime").textContent = post.time;
  document.getElementById("detailContent").textContent = post.content;
  document.getElementById("detailLikes").textContent = post.likes;
  document.getElementById("detailCommentsCount").textContent = post.comments.length;

  renderComments();

  const popup = document.getElementById("postDetailPopup");
  popup.classList.remove("hidden");

  const card = document.getElementById("postDetailCard");
  card.classList.remove("popup-exit", "popup-exit-active");
  card.classList.add("popup-enter-active");
}


function closePostDetail() {
  const card = document.getElementById("postDetailCard");
  const popup = document.getElementById("postDetailPopup");

  card.classList.remove("popup-enter-active");
  card.classList.add("popup-exit-active");

  setTimeout(() => popup.classList.add("hidden"), 200);
}


// RENDER COMMENT LIST
function renderComments() {
  const list = document.getElementById("commentList");
  list.innerHTML = "";

  selectedPost.comments.forEach(c => {
    const item = document.createElement("div");
    item.className = "bg-gray-100 p-3 rounded-lg";
    item.innerHTML = `<strong>${c.name}:</strong> ${c.text}`;
    list.appendChild(item);
  });
}


// ADD COMMENT
function addComment() {
  const input = document.getElementById("commentInput");
  const text = input.value.trim();
  if (text === "") return;

  selectedPost.comments.push({
    name: "Bạn",
    text: text
  });

  input.value = "";
  renderComments();

  document.getElementById("detailCommentsCount").textContent = selectedPost.comments.length;
}
</script>

</body>
</html>
