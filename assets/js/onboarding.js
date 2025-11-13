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

const title = document.getElementById("guideTitle");
const text = document.getElementById("guideText");
const img = document.getElementById("guideIcon");
const stepLabel = document.getElementById("stepLabel");
const progress = document.getElementById("progressBar");
const dots = document.querySelectorAll(".dot");

function updateStep() {
  const step = steps[currentStep];
  title.textContent = step.title;
  text.textContent = step.text;
  img.src = step.img;
  stepLabel.textContent = `Bước ${currentStep + 1} / ${totalSteps}`;
  progress.style.width = `${((currentStep + 1) / totalSteps) * 100}%`;

  dots.forEach((dot, i) => {
    dot.classList.toggle("bg-teal-400", i === currentStep);
    dot.classList.toggle("bg-gray-300", i !== currentStep);
  });

   // Thêm phần đổi text nút
  const nextBtn = document.getElementById("nextBtn");
  if (currentStep === totalSteps - 1) {
  nextBtn.textContent = "Bắt đầu";
  nextBtn.onclick = () => {
    window.location.href = "dangnhap.html"; // 👉 chuyển sang trang đăng nhập
  };
}
   else {
    nextBtn.textContent = "Tiếp tục";
    // Quay lại chức năng bình thường
    nextBtn.onclick = () => {
      if (currentStep < totalSteps - 1) currentStep++;
      updateStep();
    };
  }
}

document.getElementById("nextBtn").addEventListener("click", () => {
  if (currentStep < totalSteps - 1) currentStep++;
  updateStep();
});

document.getElementById("prevBtn").addEventListener("click", () => {
  if (currentStep > 0) currentStep--;
  updateStep();
});

document.getElementById("closeBtn").addEventListener("click", () => {
  document.querySelector(".fixed").classList.add("hidden");
});

updateStep();
