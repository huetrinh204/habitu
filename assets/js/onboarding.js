// onboarding.js

const steps = [
  {
    title: "Chào mừng đến với Habitu! 🐱",
    text: "Ứng dụng giúp bạn xây dựng và duy trì các thói quen lành mạnh một cách dễ dàng và vui vẻ.",
    img: "./assets/images/welcome.png"
  },
  {
    title: "Tạo Thói Quen",
    text: "Nhấn nút 'Thêm Thói Quen' ở trang chủ để tạo thói quen mới. Chọn tên, mô tả và biểu tượng yêu thích!",
    img: "./assets/images/add.png"
  },
  {
    title: "Đánh Dấu Hoàn Thành",
    text: "Mỗi ngày, đánh dấu vào ô checkbox khi bạn hoàn thành thói quen. Chuỗi ngày (streak) sẽ tăng lên!",
    img: "./assets/images/complete.png"
  },
  {
    title: "Viết Nhật Ký",
    text: "Ghi lại cảm xúc và suy nghĩ mỗi ngày trong trang Nhật Ký. Thêm tâm trạng và tag để dễ tìm kiếm!",
    img: "./assets/images/diary.png"
  },
  {
    title: "Kết Nối Cộng Đồng",
    text: "Chia sẻ hành trình của bạn, xem bài viết từ người khác, và cùng nhau động viên trong trang Cộng Đồng.",
    img: "./assets/images/post.png"
  },
  {
    title: "Theo Dõi Tiến Độ",
    text: "Xem biểu đồ và thống kê chi tiết về tiến trình của bạn trong trang Thống Kê.",
    img: "./assets/images/process.png"
  },
  {
    title: "Sẵn Sàng Bắt Đầu! 🚀",
    text: "Bạn đã sẵn sàng để xây dựng thói quen lành mạnh! Hãy bắt đầu ngay hôm nay.",
    img: "./assets/images/start.png"
  },
];

let currentStep = 0;
const totalSteps = steps.length;

// DOM Elements
const titleEl = document.getElementById("guideTitle");
const textEl = document.getElementById("guideText");
const imgEl = document.getElementById("guideIcon");
const stepLabel = document.getElementById("stepLabel");
const progressBar = document.getElementById("progressBar");
const dots = document.querySelectorAll(".dot");
const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");
const closeBtn = document.getElementById("closeBtn");

// Cập nhật nội dung bước hiện tại
function updateStep() {
  const step = steps[currentStep];
  titleEl.textContent = step.title;
  textEl.textContent = step.text;
  imgEl.src = step.img;
  stepLabel.textContent = `Bước ${currentStep + 1} / ${totalSteps}`;
  progressBar.style.width = `${((currentStep + 1) / totalSteps) * 100}%`;

  dots.forEach((dot, i) => {
    dot.classList.toggle("bg-teal-400", i === currentStep);
    dot.classList.toggle("bg-gray-300", i !== currentStep);
  });

  // Nút Tiếp Theo đổi text nếu là bước cuối
  nextBtn.textContent = currentStep === totalSteps - 1 ? "Bắt đầu" : "Tiếp Theo →";

  // Vô hiệu hóa nút Quay Lại nếu đang ở bước đầu
  prevBtn.disabled = currentStep === 0;
}

// Event listener chỉ bind 1 lần
nextBtn.addEventListener("click", () => {
  if (currentStep < totalSteps - 1) {
    currentStep++;
    updateStep();
  } else {
    // Chuyển đến dashboard
    window.location.href = "dashboard.php";
  }
});

prevBtn.addEventListener("click", () => {
  if (currentStep > 0) {
    currentStep--;
    updateStep();
  }
});

closeBtn.addEventListener("click", () => {
  document.querySelector(".fixed")?.classList.add("hidden");
});

// Khởi tạo bước đầu tiên
updateStep();
