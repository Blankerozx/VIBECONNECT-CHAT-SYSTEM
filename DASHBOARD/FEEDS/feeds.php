<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feed System</title>
  <link rel="stylesheet" href="feed.css">
</head>
<body>
  <main class="feed-wrapper">
    <!-- Create Post -->
    <section class="create-post">
      <div class="profile-pic">
        <img src="images/user.jpg" alt="Profile Picture">
      </div>
      <div class="post-box">
        <textarea placeholder="What's on your mind?"></textarea>
        <div class="post-actions">
          <div class="left">
            <label for="imageUpload" class="upload-btn">
              <i>📷</i> Photo
            </label>
            <input type="file" id="imageUpload" accept="image/*" hidden>
          </div>
          <button class="post-btn">Post</button>
        </div>
      </div>
    </section>

    <!-- Feed -->
    <section class="feed-posts">
      <article class="post">
        <header class="post-header">
          <img src="images/user.jpg" alt="User">
          <div class="info">
            <h4>@marvelous</h4>
            <small>2 hours ago</small>
          </div>
        </header>
        <div class="post-body">
          <p>Had a great time during today's match! ⚽🔥</p>
          <img src="images/sample.jpg" alt="Post image">
        </div>
        <footer class="post-footer">
          <button class="like-btn">❤️ Like</button>
          <button class="comment-btn">💬 Comment</button>
        </footer>
      </article>
    </section>
  </main>

  <script src="feed.js"></script>
</body>
</html>
