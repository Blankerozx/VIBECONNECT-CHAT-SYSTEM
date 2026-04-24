<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>VibeConnect - Friends</title>
  <link rel="stylesheet" href="friends.css">
</head>
<body>
  <div class="friends-section">

    <!-- Search -->
    <div class="search-users">
      <h3>Find Friends</h3>
      <input type="text" id="searchInput" placeholder="Search by username or email...">
      <button onclick="searchUsers()">Search</button>
      <div id="searchResults"></div>
    </div>

    <!-- Requests -->
    <div class="friend-requests">
      <h3>Friend Requests</h3>
      <ul id="requestsList"></ul>
    </div>

    <!-- Friends -->
    <div class="friends-list">
      <h3>Your Friends</h3>
      <ul id="friendsList"></ul>
    </div>
  </div>

  <script src="friends.js"></script>
</body>
</html>
