async function loadHabits() {
  const res = await fetch("../api/get_habits.php");
  const habits = await res.json();
  const container = document.getElementById("habitList");

  container.innerHTML = "";
  habits.forEach((habit) => {
    const div = document.createElement("div");
    div.className = "habit";
    div.innerHTML = `
      <input type="checkbox" id="habit-${habit.habit_id}" ${habit.status == 1 ? "checked" : ""}>
      <label for="habit-${habit.habit_id}">
        ${habit.icon || "🐾"} ${habit.habit_name}
      </label>
    `;
    container.appendChild(div);

    div.querySelector("input").addEventListener("change", async (e) => {
      const checked = e.target.checked ? 1 : 0;
      await fetch("../api/update_habit.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${habit.habit_id}&is_completed=${checked}`,
      });
      updateStats();
    });
  });

  updateStats();
}

async function updateStats() {
  const res = await fetch("../api/get_stats.php");
  const stats = await res.json();

  document.getElementById("totalHabits").textContent = stats.total;
  document.getElementById("completedToday").textContent =
    `${stats.completed}/${stats.total} (${stats.percent}%)`;
  document.getElementById("streakDays").textContent = stats.streak;
}

loadHabits();

document.getElementById("addHabitBtn").onclick = () => {
    document.getElementById("addHabitPopup").classList.remove("hidden");
};

document.getElementById("closePopup").onclick = () => {
    document.getElementById("addHabitPopup").classList.add("hidden");
};


document.addEventListener("DOMContentLoaded", function () {

    const popup = document.getElementById("addHabitPopup");
    const openBtn = document.getElementById("addHabitBtn");
    const closeBtn = document.getElementById("closePopup");

    // mở popup
    openBtn.onclick = () => popup.classList.remove("hidden");

    // đóng popup khi bấm nút X
    closeBtn.onclick = () => popup.classList.add("hidden");

    // bấm ra ngoài để đóng
    popup.onclick = (e) => {
        if (e.target === popup) popup.classList.add("hidden");
    };

});

const checkboxes = document.querySelectorAll('.habit-checkbox');

checkboxes.forEach(cb => {
    cb.addEventListener('change', function() {
        const habitId = this.dataset.habitId;
        const completed = this.checked ? 1 : 0;

        // Gửi AJAX để update completed_today và streak trong DB
        fetch('update_habit_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({habit_id: habitId, completed_today: completed})
        })
        .then(res => res.json())
        .then(data => {
            // Cập nhật giao diện
            document.querySelector('.stat-box-hoanthanh').textContent = `${data.completed_today}/${data.total} (${data.percent}%)`;
            document.querySelector('.stat-box-streak').textContent = `${data.total_streak} ngày`;
        });
    });
});