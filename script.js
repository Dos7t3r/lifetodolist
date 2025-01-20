document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("eventModal");
  const modalContent = modal.querySelector(".modal-content");
  const closeBtn = document.getElementsByClassName("close")[0];
  const eventCards = document.getElementsByClassName("event-card");

  // 为每个事件卡片添加点击事件
  Array.from(eventCards).forEach((card) => {
    card.addEventListener("click", function () {
      const eventId = this.getAttribute("data-id");
      fetchEventDetails(eventId);
    });
  });

  // 关闭模态框
  function closeModal() {
    modal.classList.remove("show");
    setTimeout(() => {
      modal.style.display = "none";
    }, 300);
  }

  closeBtn.onclick = closeModal;

  // 点击模态框外部关闭
  window.onclick = (event) => {
    if (event.target == modal) {
      closeModal();
    }
  };

  // 获取事件详情
  function fetchEventDetails(eventId) {
    fetch(`get_event_details.php?id=${eventId}`)
      .then((response) => response.json())
      .then((data) => {
        document.getElementById("modalEventName").textContent = data.name;
        document.getElementById("modalEventDescription").textContent = data.description;

        const statusText = data.completed ? "已完成" : "未完成";
        const statusClass = data.completed ? "completed" : "";
        document.getElementById("modalEventStatus").innerHTML =
          `状态: <span class="event-status ${statusClass}">${statusText}</span>`;

        if (data.completed && data.completionDate) {
          document.getElementById("modalEventCompletionDate").textContent = `完成时间: ${data.completionDate}`;
        } else {
          document.getElementById("modalEventCompletionDate").textContent = "";
        }

        document.getElementById("modalEventDetails").textContent = data.details;

        // 显示模态框
        modal.style.display = "block";
        setTimeout(() => {
          modal.classList.add("show");
        }, 10);
      })
      .catch((error) => console.error("Error:", error));
  }
});