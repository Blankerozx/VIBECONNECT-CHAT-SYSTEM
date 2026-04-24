function searchUsers() {
  const query = document.getElementById("searchInput").value.trim();
  const resultsDiv = document.getElementById("searchResults");

  resultsDiv.innerHTML = "<p>Searching...</p>";

  fetch("search_user.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ query })
  })
  .then(res => res.json())
  .then(data => {
    resultsDiv.innerHTML = "";

    if (data.status === "error") {
      resultsDiv.innerHTML = `<p style="color:red">${data.message}</p>`;
      return;
    }

    if (!data.users || data.users.length === 0) {
      resultsDiv.innerHTML = `<p style="color:orange">No matching users found.</p>`;
      return;
    }

    data.users.forEach(user => {
      const userDiv = document.createElement("div");
      userDiv.classList.add("user-result");

      let actionHTML = "";
      switch (user.status) {
        case "Request Sent":
          actionHTML = `<span class="status-badge pending">Pending</span>`;
          break;
        case "Request Received":
          actionHTML = `<span class="status-badge received">Incoming Request</span>`;
          break;
        case "Friends":
          actionHTML = `<span class="status-badge friends">Friends</span>`;
          break;
        default:
          actionHTML = `<button onclick="sendRequest(${user.id})" class="add-btn">Add Friend</button>`;
      }

      userDiv.innerHTML = `
        <div class="user-info">
          <strong>${user.username}</strong>
        </div>
        <div class="user-action">${actionHTML}</div>
      `;

      resultsDiv.appendChild(userDiv);
    });
  })
  .catch(err => {
    resultsDiv.innerHTML = `<p style="color:red">Error fetching users. Please try again.</p>`;
    console.error("Search error:", err);
  });
}


function sendRequest(receiverId) {
  fetch("send_request.php", {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify({receiver_id: receiverId})
  }).then(res => res.json())
    .then(data => {
      alert(data.message);
      loadRequests();
    });
}

function loadRequests() {
  fetch("get_request.php")
    .then(res => res.json())
    .then(data => {
      let html = "";
      data.forEach(req => {
        html += `<li>${req.username}
                   <button onclick="handleRequest(${req.request_id}, 'accepted')">Accept</button>
                   <button onclick="handleRequest(${req.request_id}, 'declined')">Decline</button>
                 </li>`;
      });
      document.getElementById("requestsList").innerHTML = html;
    });
}

function handleRequest(requestId, action) {
  fetch("handle_request.php", {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify({request_id: requestId, action: action})
  }).then(res => res.json())
    .then(data => {
      alert(data.message);
      loadRequests();
      loadFriends();
    });
}

function loadFriends() {
  fetch("friends_list.php")
    .then(res => res.json())
    .then(data => {
      let html = "";
      data.forEach(friend => {
        html += `<li>${friend.username} (${friend.email})</li>`;
      });
      document.getElementById("friendsList").innerHTML = html;
    });
}

window.onload = () => {
  loadRequests();
  loadFriends();
};
