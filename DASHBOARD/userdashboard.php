<?php
session_start();
// include __DIR__ . '/../../include/db.php';
// change this to your real connection file name
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VibeConnect Dashboard</title>
   <link rel="stylesheet" href="userdashboard.css">
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">VibeConnect</div>
    <ul class="menu">
      <li class="active"><i>🏠</i> Home</li>
      <li><i>💬</i><a href="./chat/chat.php">Chat</a></li>
      <li><i>💬</i><a href="./chat/groups.php">Groups</a></li>
      <li><i>👤</i><a href="./friends/friends.php">Friends</a></li>
      <li><i>📰</i><a href="./FEEDS/feeds.php">Feed</a></li>
      <li><i>👤</i><a href="chat.html">Profile</a></li>
      <li><i>🔔</i><a href="chat.html">Notifications</a></li>
      <li><i>⚙️</i><a href="./SETTINGS/settings.php">settings</a></li>
      <li><i>⚙️</i><a href="../include/logout.php">Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main">
 <div class="dashboard-header">
  <h2>Welcome Back 👋</h2>

  <!-- Notification Bell -->
  <div class="notification-wrapper">
    <div class="notification-icon" onclick="toggleNotifications()">
      🔔
      <span id="notifCount" class="notif-badge">0</span>
    </div>

    <!-- Dropdown -->
    <div id="notificationDropdown" class="notification-dropdown">
      <h4>Notifications</h4>
      <div id="notificationList">
        <p class="empty">No notifications</p>
      </div>
    </div>
  </div>
</div>

<div class="dashboard-grid">

  <!-- LEFT COLUMN -->
  <div class="left-column">

    <!-- Search Friends -->
    <div class="card">
      <h3>🔍 Find Friends</h3>
      <input type="text" id="searchInput" placeholder="Search friends...">
      <div id="searchResults"></div>
    </div>

    <!-- Recent Chats -->
    <div class="card">
      <h3>💬 Recent Chats</h3>
      <div id="recentChats"></div>
    </div>

  </div>

  <!-- CENTER COLUMN -->
  <div class="center-column">

    <!-- Quick Post -->
    <div class="card post-shortcut">
      <div class="post-top">
        <input type="text" placeholder="What's on your mind?" onclick="goToFeed()">
      </div>
    </div>

    <!-- Feed Preview -->
    <div class="card">
      <h3>📰 Recent Posts</h3>
      <div id="recentPosts"></div>
    </div>

  </div>

  <!-- RIGHT COLUMN -->
  <div class="right-column">

    <!-- Friend Suggestions -->
    <div class="card">
      <h3>👥 Suggested Friends</h3>
      <div id="friendSuggestions"></div>
    </div>

  </div>

</div>

</div>

<script>
function goToFeed() {
  window.location.href = "./FEEDS/feeds.php";
}

/* 🔍 Live Friend Search */
document.getElementById("searchInput").addEventListener("keyup", function () {
  let query = this.value;

  if (query.length < 1) {
    document.getElementById("searchResults").innerHTML = "";
    return;
  }

  fetch("ajax/search_friends.php?query=" + query)
    .then(res => res.text())
    .then(data => {
      document.getElementById("searchResults").innerHTML = data;
    });
});

/* Load Recent Chats */
fetch("ajax/load_recent_chats.php")
  .then(res => res.text())
  .then(data => {
    document.getElementById("recentChats").innerHTML = data;
  });

/* Load Recent Posts */
fetch("ajax/load_recent_posts.php")
  .then(res => res.text())
  .then(data => {
    document.getElementById("recentPosts").innerHTML = data;
  });

/* Load Friend Suggestions */
fetch("ajax/load_friend_suggestions.php")
  .then(res => res.text())
  .then(data => {
    document.getElementById("friendSuggestions").innerHTML = data;
  });


function addFriend(userId, button) {

    fetch("ajax/send_friend_request.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "receiver_id=" + userId
    })
    .then(res => res.text())
    .then(data => {

        if (data === "success") {

            // Change clicked button
            button.innerText = "Request Sent";
            button.disabled = true;
            button.style.backgroundColor = "gray";

            // 🔥 Refresh suggestions
            loadSuggestions();

            // 🔥 Refresh search if there's text inside
            const input = document.getElementById("searchInput");
            if (input.value.length > 0) {
                input.dispatchEvent(new Event("keyup"));
            }

        } else {
            alert(data);
        }

    });
}


function loadSuggestions() {
    fetch("ajax/load_friend_suggestions.php")
        .then(res => res.text())
        .then(data => {
            document.getElementById("friendSuggestions").innerHTML = data;
        });
}

function acceptRequestFromSearch(senderId) {

    fetch("friends/accept_from_search.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "sender_id=" + senderId
    })
    .then(res => res.text())
    .then(data => {
        alert("Friend added!");
        document.getElementById("searchInput").dispatchEvent(new Event("keyup"));
    });
}


let notifOpen = false;

// Toggle dropdown
function toggleNotifications() {
  const dropdown = document.getElementById("notificationDropdown");
  notifOpen = !notifOpen;
  dropdown.style.display = notifOpen ? "block" : "none";
}

// Close when clicking outside
document.addEventListener("click", function(e) {
  if (!e.target.closest(".notification-wrapper")) {
    document.getElementById("notificationDropdown").style.display = "none";
    notifOpen = false;
  }
});

// Load notifications
function loadNotifications() {
  fetch("ajax/load_notifications.php")
    .then(res => res.json())
    .then(data => {
      const list = document.getElementById("notificationList");
      const count = document.getElementById("notifCount");

      list.innerHTML = "";

      if (data.length === 0) {
        list.innerHTML = "<p class='empty'>No notifications</p>";
        count.innerText = "0";
        return;
      }

      count.innerText = data.length;

      data.forEach(n => {
        const div = document.createElement("div");
        div.classList.add("notification-item");

        div.innerHTML = `
          <strong>${n.text}</strong>
          <small>${n.time}</small>
        `;

        div.onclick = () => handleNotificationClick(n);

        list.appendChild(div);
      });
    });
}

// Handle click actions
function handleNotificationClick(n) {
  if (n.type === "friend_request") {
    window.location.href = "./friends/friends.php";
  } 
  else if (n.type === "message") {
    window.location.href = "./chat/chat.php";
  } 
  else if (n.type === "post") {
    window.location.href = "./FEEDS/feeds.php";
  }
}

// Auto refresh notifications
setInterval(loadNotifications, 5000);
loadNotifications();
</script>

  
   
  </div>

</body>
</html>
