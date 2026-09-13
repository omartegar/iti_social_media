//
//
//
//
//
//
//
import { getPrivateMessages } from "../../api.js";

async function openPrivateChat(userId) {
  const chatArea = document.getElementsByClassName("chat-area")[0];
  chatArea.innerHTML = "";

  async function getUserDataAndMessage(userId) {
    const response = await fetch(getPrivateMessages + `?id=${userId}`, {
      method: "GET",
      headers: { Authorization: localStorage.getItem("token") },
    });

    const data = await response.json();
    if (data.status === "success") {
      console.log(data);
    } else {
      console.error(data);
      alert(data.message);
    }
  }

  getUserDataAndMessage(userId);

  //   Append header
  chatArea.innerHTML += `
    <div class="chat-header">
          <div class="user-profile">
            <div class="avatar" id="chatAvatar">F</div>

            <div>
              <h3 id="chatName">Fares</h3>
              <span id="chatStatus">Status - Soon</span>
            </div>
          </div>

          <button class="info-btn" id="infoBtn">ⓘ</button>
        </div>`;

  // Append profile-panel
  chatArea.innerHTML += `
    <div class="profile-panel" id="profilePanel">
          <div class="profile-avatar" id="profileAvatar">F</div>

          <h2 id="profileName">Fares</h2>

          <p id="profileStatus">Online</p>

          <div class="profile-info">
            <div>
              <span>Email</span>
              <p id="profileEmail">fares@gmail.com</p>
            </div>

            <div>
              <span>Phone</span>
              <p id="profilePhone">+20 100 000 0000</p>
            </div>

            <div>
              <span>About</span>
              <p id="profileAbout">Hey there! I am using Messenger.</p>
            </div>
          </div>
        </div>

        <div class="messages" id="messages">
          <div class="message received">
            <p>Hello, how are you?</p>
            <span>10:30 AM</span>
          </div>

          <div class="message sent">
            <p>I'm good, how about you?</p>
            <span>10:31 AM</span>
          </div>

          <div class="message received">
            <p>I'm good too.</p>
            <span>10:32 AM</span>
          </div>
        </div>

        <div class="message-box">
          <input
            type="text"
            id="messageInput"
            placeholder="Write a message..."
          />

          <button id="sendBtn">Send</button>
        </div>`;
}
//
//
//
//
//
