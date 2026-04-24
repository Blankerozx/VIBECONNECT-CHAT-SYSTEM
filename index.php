<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VibeConnect - Homepage</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #f9f9f9;
      color: #333;
      text-align: center;
    }

    header {
      padding: 60px 20px 20px;
      background: linear-gradient(135deg, #ff7e5f, #feb47b);
      color: white;
    }

    header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
    }

    header p {
      font-size: 1.1rem;
      margin-bottom: 20px;
    }

    .btn {
      padding: 12px 24px;
      margin: 10px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn-start {
      background: #ff7e5f;
      color: white;
    }

    .btn-start:hover {
      background: #e56a4e;
    }

    .btn-login {
      background: white;
      border: none;
      color: #ff7e5f;
    }

    .btn-login:hover {
      background: #ff7e5f;
      color: white;
    }

    a{
      text-decoration: none;
    }

    .features {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      padding: 40px 20px;
    }

    .feature {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      transition: 0.3s ease;
    }

    .feature:hover {
      transform: translateY(-5px);
    }

    .feature i {
      font-size: 2rem;
      color: #ff7e5f;
      margin-bottom: 10px;
    }

    footer {
      background: #333;
      color: white;
      padding: 15px;
      margin-top: 30px;
      font-size: 0.9rem;
    }
    .btn-a-start{
      color: white;
    }
    .btn-a-login{
     color: #ff7e5f;
    }
    .btn-a-login:hover{
      background: #ff7e5f;
      color: white;
    }
  </style>
</head>
<body>

  <header>
    <h1>Welcome to VibeConnect</h1>
    <p>Chat. Connect. Share vibes with friends — securely and seamlessly.<br>
       Join today and experience real-time conversations without distractions.</p>
    <button class="btn btn-start"><a href="./authentication/signup.php" class="btn-a-start">Get Started</a></button>
    <button class="btn btn-login"><a href="./authentication/loginpage.php" class="btn-a-login">Log In</a></button>

  </header>

  <section class="features">
    <div class="feature">
      <i class="fa-solid fa-lock"></i>
      <h3>Secure</h3>
      <p>Passwords are safely encrypted with the latest security practices.</p>
    </div>
    <div class="feature">
      <i class="fa-solid fa-bolt"></i>
      <h3>Fast Messaging</h3>
      <p>Real-time chats with no delays  built lightweight and efficient.</p>
    </div>
    <div class="feature">
      <i class="fa-solid fa-users"></i>
      <h3>Connect</h3>
      <p>Add friends, grow your network, and chat in a welcoming space.</p>
    </div>
    <div class="feature">
      <i class="fa-solid fa-mobile-screen-button"></i>
      <h3>Simple Design</h3>
      <p>Clean, modern, and easy-to-use interface across all devices.</p>
    </div>
  </section>

  <footer>
    © 2025 VibeConnect. All rights reserved.
  </footer>

</body>
</html>