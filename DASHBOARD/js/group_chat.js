// Load groups when page loads
document.addEventListener("DOMContentLoaded", loadGroups);

function loadGroups() {
  fetch("../chat/fetch_groups.php")
    .then((res) => res.json())
    .then((data) => {
      const list = document.getElementById("groupsList");
      list.innerHTML = "";

      if (!data.length) {
        list.innerHTML = "<p>No groups available.</p>";
        return;
      }

      data.forEach((g) => {
        const div = document.createElement("div");
        div.textContent = g.group_name;
        div.classList.add("group-item");

        div.onclick = () => {
          console.log("Opening group:", g.group_id, g.group_name);
          openGroupChat(g.group_id, g.group_name, div);
        };

        list.appendChild(div);
      });
    })
    .catch((err) => console.error("Error loading groups:", err));
}

// Create new group
document.getElementById("createGroupBtn").addEventListener("click", () => {
  const name = prompt("Enter group name:");
  if (!name) return;

  fetch("../chat/create_group.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ group_name: name }),
  })
    .then((res) => res.json())
    .then((res) => {
      if (res.success) loadGroups();
      else alert("Failed to create group");
    })
    .catch((err) => console.error("Error creating group:", err));
});

function openGroupChat(id, name, element) {
  if (!id) {
    console.error("❌ Invalid group id passed to openGroupChat:", id);
    alert("Error: Invalid group selected.");
    return;
  }

  // Store current group globally
  window.currentGroupId = id;

  // Highlight active group
  document.querySelectorAll(".group-item").forEach((el) => el.classList.remove("active"));
  element.classList.add("active");

  // Set chat title
  document.getElementById("groupName").textContent = name;
  const chatBox = document.getElementById("chatBox");
  chatBox.innerHTML = "<p>Loading messages...</p>";

  // Fetch messages
  fetch(`../chat/fetch_group_messages.php?group_id=${id}`)
    .then((res) => res.json())
    .then((messages) => {
      chatBox.innerHTML = "";
      if (!messages.length) {
        chatBox.innerHTML = "<p>No messages yet.</p>";
        return;
      }

      messages.forEach((msg) => {
        const div = document.createElement("div");
        div.classList.add("message");
        div.classList.add(msg.is_self ? "self" : "other");
        div.innerHTML = `<strong>${msg.username}:</strong> ${msg.message}`;
        chatBox.appendChild(div);
      });

      chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch((err) => {
      console.error("Error fetching messages:", err);
      chatBox.innerHTML = "<p>Failed to load messages.</p>";
    });

  // Load members
  loadGroupMembers(id);

  // Send message logic
  const sendBtn = document.getElementById("sendBtn");
  const msgInput = document.getElementById("messageInput");
  sendBtn.onclick = null; // clear previous event

  sendBtn.onclick = () => {
    const msg = msgInput.value.trim();
    if (!msg) return alert("Type a message first.");

    const payload = { group_id: id, message: msg };
    fetch("../chat/send_group_message.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          msgInput.value = "";
          openGroupChat(id, name, element); // reload messages
        } else {
          alert("Message failed: " + data.error);
        }
      })
      .catch((err) => console.error("Fetch error:", err));
  };
}

// Load group members
function loadGroupMembers(groupId) {
  const memberList = document.getElementById("memberList");
  const addBtn = document.getElementById("addMemberBtn");
  const leaveBtn = document.getElementById("leaveGroupBtn");
  const removeBtn = document.getElementById("removeMemberBtn");

  // show loading message
  memberList.innerHTML = "<p>Loading members...</p>";

  fetch(`../chat/fetch_group_members.php?group_id=${groupId}`)
    .then((res) => {
      if (!res.ok) throw new Error("Failed to fetch group members");
      return res.json();
    })
    .then((data) => {
      memberList.innerHTML = "";

      if (!Array.isArray(data) || data.length === 0) {
        memberList.innerHTML = "<p>No members yet.</p>";
        addBtn.classList.add("hidden");
         leaveBtn.classList.add("hidden");
        removeBtn.classList.add("hidden");
        return;
      }

      console.log("Current User ID:", window.currentUserId);
console.log("Members data:", data);


      // render each member
    data.forEach((m) => {
  const p = document.createElement("p");
  p.classList.add("member-item");

  if (m.role === "admin") {
    p.classList.add("admin-member");
  } else {
    p.classList.add("regular-member");
  }

  p.innerHTML = `
    <span class="member-name">${m.username}</span>
    ${
      m.role === "admin"
        ? "<span class='badge admin-badge'>Admin 👑</span>"
        : "<span class='badge member-badge'>Member</span>"
    }
  `;

  // if admin (current user) -> show remove button for others
  const currentUserId = parseInt(window.currentUserId || 0);
  const admin = data.find((u) => u.role === "admin");

  if (admin && parseInt(admin.id) === currentUserId && m.role !== "admin") {
    const btn = document.createElement("button");
    btn.textContent = "Remove";
    btn.classList.add("remove-btn");
    btn.onclick = () => removeMember(groupId, m.id, m.username);
    p.appendChild(btn);
  }

  // if current user is the same member (not admin)
  if (parseInt(m.id) === currentUserId && m.role !== "admin") {
    const btn = document.createElement("button");
    btn.textContent = "Leave";
    btn.classList.add("leave-btn");
    btn.onclick = () => leaveGroup(groupId);
    p.appendChild(btn);
  }

  memberList.appendChild(p);
});

// determine if current user is admin
const currentUserId = parseInt(window.currentUserId || 0);
const admin = data.find((u) => u.role === "admin");

if (admin && parseInt(admin.id) === currentUserId) {
  addBtn.classList.remove("hidden");
  addBtn.onclick = () => showFriendList(groupId);
} else {
  addBtn.classList.add("hidden");
}
})
.catch((err) => {
  console.error("Error loading members:", err);
  memberList.innerHTML =
    "<p class='error'>Failed to load group members. Please try again.</p>";
});
}
// remove member
// --- Leave Group ---
function leaveGroup(groupId) {
  if (!confirm("Are you sure you want to leave this group?")) return;

  fetch("../chat/leave_group.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `group_id=${groupId}`,
  })
    .then((res) => res.json())
    .then((data) => {
      alert(data.message);
      if (data.success) {
        disableChatInput("You left the group.");
        document.getElementById("memberList").innerHTML =
          "<p>You left this group.</p>";
      }
    })
    .catch((err) => console.error("Error leaving group:", err));
}

// --- Remove Member (Admin only) ---
function removeMember(groupId, memberId, username) {
  if (!confirm(`Remove ${username} from this group?`)) return;

  fetch("../chat/remove_member.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `group_id=${groupId}&member_id=${memberId}`,
  })
    .then((res) => res.json())
    .then((data) => {
      alert(data.message);
      if (data.success) {
        loadGroupMembers(groupId);

        // Insert system message in chat UI for everyone else
        const chatBox = document.getElementById("chatBox");
        const sys = document.createElement("div");
        sys.classList.add("system-message");
        sys.textContent = `${username} was removed by the admin.`;
        chatBox.appendChild(sys);
        chatBox.scrollTop = chatBox.scrollHeight;
      }
    })
    .catch((err) => console.error("Error removing member:", err));
}



// Show modal with friend list
function showFriendList() {
  const modal = document.getElementById("friendModal");
  modal.classList.remove("hidden");

  console.log("Fetching friends list..."); // ✅ debug line

  fetch("../chat/fetch_group_friends.php")
    .then((res) => {
      console.log("Response status:", res.status); // ✅
      return res.json();
    })
    .then((friends) => {
      console.log("Friends data:", friends); // ✅ view what was returned

      const list = document.getElementById("friendsList");
      list.innerHTML = "";

      if (!friends.length) {
        list.innerHTML = "<p>No friends found.</p>";
        return;
      }

      friends.forEach((f) => {
        const div = document.createElement("div");
        div.classList.add("friend-item");
        div.innerHTML = `
          <span>${f.username}</span>
          <button class="add-btn" data-id="${f.id}">Add</button>
        `;
        list.appendChild(div);
      });

      document.querySelectorAll(".add-btn").forEach((btn) => {
        btn.onclick = () => addFriendToGroup(btn.dataset.id);
      });
    })
    .catch((err) => console.error("Error loading friends:", err));
}


// Add friend to current group
function addFriendToGroup(friendId) {
  if (!window.currentGroupId) {
    alert("Select a group first!");
    return;
  }

  fetch("../chat/add_members.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ group_id: window.currentGroupId, friend_id: friendId }),
  })
    .then((res) => res.json())
    .then((data) => {
      alert(data.message);
      if (data.success) {
        loadGroupMembers(window.currentGroupId);
        document.getElementById("friendModal").classList.add("hidden");
      }
    })
    .catch((err) => console.error("Error adding member:", err));
}

// Close modal
document.getElementById("closeModal").onclick = () => {
  document.getElementById("friendModal").classList.add("hidden");
};



function disableChatInput(reasonText) {
  const msgInput = document.getElementById("messageInput");
  const sendBtn = document.getElementById("sendBtn");
  const chatBox = document.getElementById("chatBox");

  msgInput.disabled = true;
  sendBtn.disabled = true;
  msgInput.placeholder = reasonText || "You cannot send messages.";

  const systemMsg = document.createElement("div");
  systemMsg.classList.add("system-message");
  systemMsg.textContent = reasonText;
  chatBox.appendChild(systemMsg);
  chatBox.scrollTop = chatBox.scrollHeight;
}
