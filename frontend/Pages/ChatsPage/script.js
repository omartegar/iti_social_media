const themeBtn = document.getElementById("themeBtn");
const searchInput = document.getElementById("searchInput");

themeBtn.addEventListener("click", () => {
  document.body.classList.toggle("dark");
  themeBtn.textContent = document.body.classList.contains("dark") ? "☀" : "☾";
});

document.addEventListener("click", (event) => {
  if (event.target.closest("#refreshChatBtn")) {
    window.refreshActiveChat?.();
    return;
  }

  if (event.target.closest(".info-btn")) {
    document.getElementById("profilePanel")?.classList.toggle("show");
  }

  if (event.target.closest("#sendBtn")) {
    sendMessage();
  }
});

document.addEventListener("keydown", (event) => {
  if (event.key === "Enter" && event.target.id === "messageInput") {
    sendMessage();
  }
});

async function sendMessage() {
  const messageInput = document.getElementById("messageInput");
  const messages = document.getElementById("messages");
  const sendButton = document.getElementById("sendBtn");
  const sendStatus = document.getElementById("sendStatus");
  const text = messageInput?.value.trim();

  if (!messageInput || !messages || !text || !window.activeChatUserId) return;

  sendButton.disabled = true;
  sendStatus.textContent = "Sending...";
  sendStatus.className = "send-status";

  try {
    const formData = new FormData();
    formData.append("receiver_id", window.activeChatUserId);
    formData.append("message", text);

    const response = await fetch(window.sendPrivateMessageUrl, {
      method: "POST",
      headers: { Authorization: localStorage.getItem("token") },
      body: formData,
    });
    const data = await response.json();

    if (data.status !== "success") {
      throw new Error(data.message || "Message could not be sent");
    }

    const emptyState = messages.querySelector(".messages-empty-state");
    emptyState?.remove();

    const message = document.createElement("div");
    message.className = "message sent";
    message.innerHTML = `<p></p><span>Now</span>`;
    message.querySelector("p").textContent = text;
    messages.appendChild(message);

    messageInput.value = "";
    sendStatus.textContent = "Sent";
    messages.scrollTop = messages.scrollHeight;
  } catch (error) {
    sendStatus.textContent = error.message;
    sendStatus.className = "send-status error";
  } finally {
    sendButton.disabled = false;
  }
}

searchInput.addEventListener("input", () => {
  const value = searchInput.value.toLowerCase().trim();

  document.querySelectorAll(".friend").forEach((friend) => {
    const name = friend.querySelector("h3")?.textContent.toLowerCase() || "";
    friend.style.display = name.includes(value) ? "flex" : "none";
  });
});
