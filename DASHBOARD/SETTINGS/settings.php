<?php
session_start();
include __DIR__ . '/../../include/db.php'; // adjust path if needed

// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../../login.php");
//     exit();
// }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="settings.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  
<div class="settings-container">
  <h2>Account Settings</h2>

  <!-- Profile Section -->
  <section class="settings-section">
    <h3>Profile</h3>
    <div class="profile-pic">
      <img id="profilePreview" src="uploads/<?php echo $_SESSION['profile_pic'] ?? 'default.png'; ?>" alt="Profile">
      <input type="file" id="profilePicInput" accept="image/*">
      <button id="uploadBtn">Change Photo</button>
    </div>

    <label>Username</label>
    <input type="text" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">

    <label>Email</label>
    <input type="email" id="email" value="<?php ; ?>">
    <!-- i disabled echo htmlspecialchars($_SESSION['email']); so remember pls -->

    <button id="saveProfileBtn">Save Changes</button>
  </section>

  <!-- Password Section -->
  <section class="settings-section">
    <h3>Change Password</h3>
    <label>Current Password</label>
    <input type="password" id="currentPassword">

    <label>New Password</label>
    <input type="password" id="newPassword">

    <label>Confirm New Password</label>
    <input type="password" id="confirmPassword">

    <button id="updatePasswordBtn">Update Password</button>
  </section>

  <p id="statusMessage" class="status"></p>
</div>

<script src="settings.js"></script>


</body>
</html>