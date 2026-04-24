<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Login — VibeConnect</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
  <div class="container">
    <div class="panel-left">
      <h1>Welcome back</h1>
      <p>Sign in to continue chatting with your friends and access your VibeConnect account.</p>
    </div>

    <div class="panel-right">
      <div class="brand">
        <div class="logo">V</div>
        <div>
          <h2>VibeConnect</h2>
          <p>Sign in to your account</p>
        </div>
      </div>

      <form action="../include/login.inc.php" method="POST" novalidate>
        <div class="input-group">
          <label for="login-username">Username</label>
          <input id="login-username" class="input" name="username" type="text" placeholder="Enter your username" required>
        </div>

        <div class="input-group">
          <label for="login-password">Password</label>
          <input id="login-password" class="input" name="password" type="password" placeholder="Enter your password" required>
        </div>

        <button class="btn" type="submit" name="login">Sign in</button>

        <div class="form-footer">
          <span>Don't have an account?</span>
          <a href="signup.php">Create one</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>