<?php
session_start();
include __DIR__ . '/../../include/db.php';

if (!isset($_SESSION['id'])) {
    header("Location: ./authentication/loginpage.php");
    exit;
}

$user_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Private Chat</title>
    <link rel="stylesheet" href="chat.css">
</head>
<body>

<div class="chat-container">
    <div class="sidebar">
        <h3>Friends</h3>
        <div id="friendsList"></div>
    </div>

    <div class="chat-window">
        <div id="chatHeader">Select a friend to start chatting</div>
        <div id="chatMessages"></div>

        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Type a message...">
            <button id="sendBtn">Send</button>
        </div>
    </div>
</div>

<script>
let currentReceiver = null;

// Fetch friends list
fetch('fetch_friends.php')
  .then(res => res.json())
  .then(data => {
    const container = document.getElementById('friendsList');
    container.innerHTML = "";
    data.forEach(friend => {
      const div = document.createElement('div');
      div.classList.add('friend');
      div.textContent = friend.username;
      div.onclick = () => openChat(friend.id, friend.username);
      container.appendChild(div);
    });
  });

// Open chat with selected friend
function openChat(id, name) {
  currentReceiver = id;
  document.getElementById('chatHeader').textContent = "Chat with " + name;
  loadMessages();

  // Refresh messages every 2s
  setInterval(() => {
    if (currentReceiver) loadMessages();
  }, 2000);
}

// Load messages
function loadMessages() {
  fetch('fetch_messages.php?receiver_id=' + currentReceiver)
    .then(res => res.json())
    .then(messages => {
      const box = document.getElementById('chatMessages');
      box.innerHTML = '';
      messages.forEach(msg => {
        const div = document.createElement('div');
        div.classList.add(msg.is_sender ? 'my-msg' : 'their-msg');
        div.textContent = msg.message;
        box.appendChild(div);
      });
      box.scrollTop = box.scrollHeight;
    });
}

// Send message
document.getElementById('sendBtn').onclick = () => {
  const msg = document.getElementById('messageInput').value.trim();
  if (!msg || !currentReceiver) return;

  fetch('send_message.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({receiver_id: currentReceiver, message: msg})
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById('messageInput').value = '';
    loadMessages();
  });
};
</script>
</body>
</html>
