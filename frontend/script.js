const friends = document.querySelectorAll(".friend");

const chatName = document.getElementById("chatName");
const chatStatus = document.getElementById("chatStatus");
const chatAvatar = document.getElementById("chatAvatar");

const messages = document.getElementById("messages");
const messageInput = document.getElementById("messageInput");
const sendBtn = document.getElementById("sendBtn");

const themeBtn = document.getElementById("themeBtn");

const infoBtn = document.getElementById("infoBtn");
const profilePanel = document.getElementById("profilePanel");

const profileName = document.getElementById("profileName");
const profileStatus = document.getElementById("profileStatus");
const profileAvatar = document.getElementById("profileAvatar");
const profileEmail = document.getElementById("profileEmail");
const profilePhone = document.getElementById("profilePhone");
const profileAbout = document.getElementById("profileAbout");

const searchInput = document.getElementById("searchInput");

friends.forEach(friend => {

    friend.addEventListener("click", () => {

        friends.forEach(item => {
            item.classList.remove("active");
        });

        friend.classList.add("active");

        const name = friend.dataset.name;
        const status = friend.dataset.status;
        const email = friend.dataset.email;
        const phone = friend.dataset.phone;
        const about = friend.dataset.about;
        const message = friend.dataset.message;

        chatName.textContent = name;
        chatStatus.textContent = status;
        chatAvatar.textContent = name.charAt(0);

        profileName.textContent = name;
        profileStatus.textContent = status;
        profileAvatar.textContent = name.charAt(0);
        profileEmail.textContent = email;
        profilePhone.textContent = phone;
        profileAbout.textContent = about;

        messages.innerHTML = `
            <div class="message received">
                <p>${message}</p>
                <span>10:30 AM</span>
            </div>

            <div class="message sent">
                <p>Sounds good!</p>
                <span>10:31 AM</span>
            </div>

            <div class="message received">
                <p>Okay, see you soon.</p>
                <span>10:32 AM</span>
            </div>
        `;

        profilePanel.classList.remove("show");
    });

});

infoBtn.addEventListener("click", () => {
    profilePanel.classList.toggle("show");
});

function sendMessage() {

    const text = messageInput.value.trim();

    if (text === "") {
        return;
    }

    const message = document.createElement("div");

    message.classList.add("message", "sent");

    message.innerHTML = `
        <p>${text}</p>
        <span>Now</span>
    `;

    messages.appendChild(message);

    messageInput.value = "";

    messages.scrollTop = messages.scrollHeight;
}

sendBtn.addEventListener("click", sendMessage);

messageInput.addEventListener("keydown", event => {

    if (event.key === "Enter") {
        sendMessage();
    }

});

themeBtn.addEventListener("click", () => {

    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {
        themeBtn.textContent = "☀";
    } else {
        themeBtn.textContent = "☾";
    }

});

searchInput.addEventListener("input", () => {

    const value = searchInput.value.toLowerCase();

    friends.forEach(friend => {

        const name = friend.dataset.name.toLowerCase();

        if (name.includes(value)) {
            friend.style.display = "flex";
        } else {
            friend.style.display = "none";
        }

    });

});