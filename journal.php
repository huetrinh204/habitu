<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: dangnhap.php");
    exit();
}
include "config.php";

// Lấy user_id & username
$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// Thêm nhật ký mới
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['title'])){
    $journal_date = $_POST['journal_date'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $icon = $_POST['icon'] ?? '';

    if($journal_date && $title && $content){
        $stmt = $pdo->prepare("INSERT INTO health_journal (journal_date, title, content, icon, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$journal_date, $title, $content, $icon, $user_id]);
        $id = $pdo->lastInsertId();

        echo json_encode([
            'success'=>true,
            'journal'=>[
                'id'=>$id,
                'title'=>$title,
                'content'=>$content,
                'journal_date'=>$journal_date,
                'icon'=>$icon
            ]
        ]);
        exit();
    }
}

// Lấy danh sách nhật ký
$stmt = $pdo->prepare("SELECT * FROM health_journal WHERE user_id=? ORDER BY journal_date DESC");
$stmt->execute([$user_id]);
$journals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<body style="background: linear-gradient(to right, #00c8ffb2, #006ef5c0)";>
<?php include "navbar.php"; ?>

<div class="mb-6 text-center mt-10">

    <h1 class="text-2xl font-bold text-white leading-loose">
        Nhật Ký Của Bạn 📘
    </h1>

    <p class="text-sm text-white leading-relaxed">
        Ghi lại cảm xúc và suy nghĩ mỗi ngày ✨ 
    </p>
</div>


<section class="container mx-auto mt-8 px-6">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-semibold text-gray-700">Danh sách nhật ký</h3>
    <button id="openJournalPopup" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md shadow">
      + Viết Nhật Ký
    </button>
  </div>

  <div id="journalList" class="space-y-4">
    <?php foreach($journals as $j): ?>
      <div class="bg-white p-4 rounded-xl shadow-md cursor-pointer flex items-center justify-between journal-item"
           data-id="<?= $j['journal_id'] ?>"
           data-title="<?= htmlspecialchars($j['title']) ?>"
           data-content="<?= htmlspecialchars($j['content']) ?>"
           data-date="<?= $j['journal_date'] ?>"
           data-icon="<?= htmlspecialchars($j['icon']) ?>">
        <div class="flex items-center gap-3">
          <div class="text-2xl"><?= htmlspecialchars($j['icon']) ?></div>
          <div>
            <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($j['title']) ?></h4>
            <p class="text-gray-500 text-sm"><?= date("d/m/Y", strtotime($j['journal_date'])) ?></p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <div class="text-gray-400 hover:text-gray-600 cursor-pointer view-btn">➡️</div>
          <div class="text-red-500 hover:text-red-700 cursor-pointer delete-btn">🗑️</div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- POPUP VIẾT NHẬT KÝ -->
<div id="journalPopup" class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center hidden z-50">
  <div class="bg-white w-[550px] p-6 rounded-xl shadow-xl">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-semibold">Viết Nhật Ký Mới</h3>
      <button onclick="closeJournalPopup()" class="text-gray-500 text-xl hover:text-black">×</button>
    </div>
    <form id="journalForm" class="space-y-4">
      <input type="hidden" name="user_id" value="<?= $user_id ?>">
      <div>
        <label class="font-semibold">Ngày</label>
        <input name="journal_date" type="date" class="w-full border px-3 py-2 rounded-md mt-1" required>
      </div>
      <div>
        <label class="font-semibold">Tiêu đề</label>
        <input name="title" type="text" placeholder="Nhập tiêu đề..." class="w-full border px-3 py-2 rounded-md mt-1" required>
      </div>
      <div>
        <label class="font-semibold">Nội dung</label>
        <textarea name="content" rows="4" placeholder="Chia sẻ cảm xúc..." class="w-full border px-3 py-2 rounded-md mt-1" required></textarea>
      </div>
      <div>
        <label class="font-semibold">Cảm xúc hôm nay</label>
        <input id="emotionInput" type="text" readonly placeholder="Chọn cảm xúc..." class="w-full border px-3 py-2 rounded-md cursor-pointer" required>
        <div id="emotionGrid" class="absolute bg-white border rounded-lg shadow-lg p-2 grid grid-cols-5 gap-2 mt-2 hidden max-h-48 overflow-y-auto z-50">
          <?php $emotions = ['😊','😄','😐','😔','😢','😡','💪','✨'];
          foreach($emotions as $emo) echo "<div class='cursor-pointer text-2xl text-center p-2 rounded-lg hover:bg-purple-200'>$emo</div>"; ?>
        </div>
        <input type="hidden" name="icon" id="selectedEmotion" required>
      </div>
      <div class="flex justify-end gap-3 mt-6">
        <button type="button" onclick="closeJournalPopup()" class="px-4 py-2 border rounded-md hover:bg-gray-100">Hủy</button>
        <button type="submit" class="px-5 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Lưu</button>
      </div>
    </form>
  </div>
</div>

<!-- POPUP XEM CHI TIẾT -->
<div id="viewJournalPopup" class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center hidden z-50">
  <div class="bg-white w-[500px] p-6 rounded-xl shadow-xl">
    <div class="flex justify-between items-center mb-4">
      <h3 id="viewJournalTitle" class="text-lg font-semibold"></h3>
      <button onclick="document.getElementById('viewJournalPopup').classList.add('hidden')" class="text-gray-500 text-xl hover:text-black">×</button>
    </div>
    <p class="text-gray-500 mb-2" id="viewJournalDate"></p>
    <div class="text-3xl mb-4" id="viewJournalIcon"></div>
    <p id="viewJournalContent" class="text-gray-700"></p>
  </div>
</div>

<!-- POPUP THÔNG BÁO -->
<div id="saveMessage" 
     class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow-lg hidden z-50">
</div>

<?php include "footer.php"; ?>

<script>
const popup = document.getElementById("journalPopup");
const journalForm = document.getElementById("journalForm");
const emotionInput = document.getElementById("emotionInput");
const emotionGrid = document.getElementById("emotionGrid");
const selectedEmotion = document.getElementById("selectedEmotion");
const saveMsg = document.getElementById("saveMessage");

// Mở popup viết nhật ký
document.getElementById("openJournalPopup").addEventListener("click", ()=> popup.classList.remove("hidden"));
function closeJournalPopup(){
    popup.classList.add("hidden");
    journalForm.reset();
    selectedEmotion.value="";
    emotionInput.value="";
    emotionGrid.querySelectorAll('div').forEach(d=>d.classList.remove('bg-purple-200'));
}

// Chọn cảm xúc
emotionInput.addEventListener('click', ()=> emotionGrid.classList.toggle('hidden'));
emotionGrid.querySelectorAll('div').forEach(div=>{
    div.addEventListener('click', ()=>{
        selectedEmotion.value = div.textContent;
        emotionInput.value = div.textContent;
        emotionGrid.classList.add('hidden');
        emotionGrid.querySelectorAll('div').forEach(d=>d.classList.remove('bg-purple-200'));
        div.classList.add('bg-purple-200');
    });
});
document.addEventListener('click', e=>{
    if(!emotionInput.contains(e.target) && !emotionGrid.contains(e.target)){
        emotionGrid.classList.add('hidden');
    }
});

// Xem chi tiết & xoá
function attachEvents(div){
    // Xem chi tiết
    div.querySelector('.view-btn').addEventListener('click', e=>{
        e.stopPropagation();
        document.getElementById("viewJournalTitle").textContent = div.dataset.title;
        document.getElementById("viewJournalDate").textContent = div.dataset.date;
        document.getElementById("viewJournalIcon").textContent = div.dataset.icon;
        document.getElementById("viewJournalContent").textContent = div.dataset.content;
        document.getElementById("viewJournalPopup").classList.remove("hidden");
    });

    // Xoá nhật ký
    div.querySelector('.delete-btn').addEventListener('click', e=>{
        e.stopPropagation();
        if(!confirm("Bạn có chắc muốn xoá nhật ký này?")) return;

        fetch("delete_journal.php", {
            method: "POST",
            headers: {"Content-Type":"application/x-www-form-urlencoded"},
            body: new URLSearchParams({journal_id: div.dataset.id})
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                div.remove();
                saveMsg.textContent = "✅ Nhật ký đã được xoá!";
                saveMsg.classList.remove("hidden");
                setTimeout(()=> saveMsg.classList.add("hidden"),2500);
            } else {
                alert("Lỗi khi xoá nhật ký!");
            }
        });
    });
}

// Gán sự kiện cho các item hiện có
document.querySelectorAll('.journal-item').forEach(div=> attachEvents(div));

// Lưu nhật ký mới
journalForm.addEventListener("submit", async function(e){
    e.preventDefault();
    const formData = new FormData(journalForm);
    if(!formData.get("icon")){ alert("Vui lòng chọn cảm xúc!"); return; }

    const res = await fetch(window.location.href, {method:"POST", body:formData});
    const data = await res.json();

    if(data.success){
        const j = data.journal;
        const div = document.createElement("div");
        div.className = "bg-white p-4 rounded-xl shadow-md cursor-pointer flex items-center justify-between journal-item";
        div.dataset.id = j.id;
        div.dataset.title = j.title;
        div.dataset.content = j.content;
        div.dataset.date = j.journal_date;
        div.dataset.icon = j.icon;
        div.innerHTML = `
          <div class="flex items-center gap-3">
            <div class="text-2xl">${j.icon}</div>
            <div>
              <h4 class="font-semibold text-gray-800">${j.title}</h4>
              <p class="text-gray-500 text-sm">${new Date(j.journal_date).toLocaleDateString()}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <div class="text-gray-400 hover:text-gray-600 cursor-pointer view-btn">➡️</div>
            <div class="text-red-500 hover:text-red-700 cursor-pointer delete-btn">🗑️</div>
          </div>
        `;
        document.getElementById("journalList").prepend(div);
        attachEvents(div);

        closeJournalPopup();
        saveMsg.textContent = "✅ Nhật ký đã được lưu!";
        saveMsg.classList.remove("hidden");
        setTimeout(()=> saveMsg.classList.add("hidden"),2500);
    } else alert("Lỗi khi lưu nhật ký!");
});
</script>
</body>
</html>
