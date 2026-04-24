<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Sign Up — VibeConnect</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
  <div class="container">
    <div class="panel-left">
      <h1>Welcome to VibeConnect</h1>
      <p>Join a friendly space to chat, share moments, and connect with people. Secure accounts, fast messaging — built lightweight for real conversations.</p>
    </div>

    <div class="panel-right">
      <div class="brand">
        <div class="logo">V</div>
        <div>
          <h2>VibeConnect</h2>
          <p>Create your account</p>
        </div>
      </div>

      <!-- message placeholder -->
      <!-- <div class="message">Signup successful — check your email.</div> -->

      <form action="../include/signup.inc.php" method="POST" novalidate>
        <div class="input-group">
          <label for="username">Username</label>
          <input id="username" class="input" name="username" type="text" placeholder="Enter username" required>
        </div>

        <div class="input-group">
          <label for="email">Email</label>
          <input id="email" class="input" name="email" type="email" placeholder="you@example.com" required>
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input id="password" class="input" name="password" type="password" placeholder="Create a strong password" required>
        </div>

        <button class="btn" type="submit" name="submit">Create account</button>

        <div class="form-footer">
          <span>Already have an account?</span>
          <a href="loginpage.php">Log in</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>