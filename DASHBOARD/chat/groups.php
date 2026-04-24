<?php
// if (!isset($_SESSION['id'])) {
//   header("Location: ../../loginpage.php");
//   exit;
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Group Chat</title>
  <link rel="stylesheet" href="groups.css" />
</head>
<body>
  <!-- Main Group Container -->
  <div class="group-container">

    <!-- Sidebar: Group List -->
    <aside class="group-sidebar">
      <div class="sidebar-header">
        <h2>Groups</h2>
        <button id="createGroupBtn">+ New Group</button>
      </div>
      <div id="groupsList" class="groups-list">
        <!-- Groups dynamically loaded here -->
      </div>
    </aside>

    <!-- Chat Section -->
    <main class="group-chat">
      <div class="chat-header" id="groupName">
        Select a group to start chatting
      </div>

      <div class="chat-box" id="chatBox">
        <!-- Messages dynamically inserted -->
      </div>

      <div class="chat-input">
        <input type="text" id="messageInput" placeholder="Type a message..." />
        <button id="sendBtn">Send</button>
      </div>
    </main>

    <!-- Right Sidebar: Group Members -->
    <aside class="group-members" id="groupMembers">
      <div class="members-header">
        <h3>Members</h3>
      </div>

      <div id="memberList">
        <!-- Members dynamically inserted -->
      </div>

      <div class="member-actions">
        <button id="addMemberBtn" class="hidden" onclick="showFriendList()">+ Add Member</button>
      </div>
    </aside>
  </div>

  <!-- Friend Modal (outside container) -->
  <div id="friendModal" class="modal hidden">
    <div class="modal-content">
      <span id="closeModal" class="close">&times;</span>
      <h3>Select Friend to Add</h3>
      <div id="friendsList"></div>
    </div>
  </div>

  <?php session_start(); ?>
<script>
  window.currentUserId = <?php echo isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0; ?>;
</script>

  
  <script src="../js/group_chat.js"></script>
</body>
</html>
