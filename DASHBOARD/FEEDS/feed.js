document.addEventListener("DOMContentLoaded", () => {
  const postForm = document.querySelector(".post-box");
  const textarea = postForm.querySelector("textarea");
  const postBtn = postForm.querySelector(".post-btn");
  const imageInput = document.getElementById("imageUpload");
  const feedContainer = document.querySelector(".feed-posts");

  // Load posts on startup
  fetchPosts();

  // Handle Post Submit
  postBtn.addEventListener("click", async () => {
    const content = textarea.value.trim();
    if (!content && !imageInput.files.length)
      return alert("Write something first!");

    const formData = new FormData();
    formData.append("content", content);
    if (imageInput.files[0]) formData.append("image", imageInput.files[0]);

    const res = await fetch("create_post.php", {
      method: "POST",
      body: formData,
    });

    const data = await res.json();
    if (data.success) {
      textarea.value = "";
      imageInput.value = "";
      fetchPosts(); // reload feed
    } else {
      alert("Failed to post");
    }
  });

  // Fetch posts (friends-only feed)
  async function fetchPosts() {
    const res = await fetch("fetch_posts.php"); // only returns posts by friends
    const posts = await res.json();

    if (!posts.length) {
      feedContainer.innerHTML = "<p>No posts to show. Add some friends!</p>";
      return;
    }

    feedContainer.innerHTML = posts
      .map(
        (p) => `
      <article class="post" data-id="${p.id}">
        <header class="post-header">
          <img src="${p.profile_pic || 'images/default.png'}" alt="User">
          <div class="info">
            <h4>@${p.username}</h4>
            <small>${timeAgo(p.created_at)}</small>
          </div>
          ${p.is_owner ? `<button class="delete-btn" onclick="deletePost(${p.id})">🗑️</button>` : ""}
        </header>

        <div class="post-body">
          <p>${p.content || ""}</p>
          ${p.image ? `<img src="${p.image}" alt="Post image">` : ""}
        </div>

        <footer class="post-actions">
          <button class="like-btn" data-liked="${p.liked}" onclick="toggleLike(${p.id}, this)">
            ❤️ ${p.like_count}
          </button>
          <button class="comment-btn" onclick="toggleComments(${p.id})">
            💬 ${p.comment_count}
          </button>
        </footer>

        <div class="comments-section" id="comments-${p.id}" style="display:none;">
          <div class="comments-list"></div>
          <div class="comment-form">
            <input type="text" placeholder="Write a comment..." id="comment-input-${p.id}" />
            <button onclick="addComment(${p.id})">Post</button>
          </div>
        </div>
      </article>
    `
      )
      .join("");
  }

  // Time formatting
  function timeAgo(dateString) {
    const date = new Date(dateString);
    const diff = (Date.now() - date) / 1000;
    if (diff < 60) return "Just now";
    if (diff < 3600) return Math.floor(diff / 60) + "m ago";
    if (diff < 86400) return Math.floor(diff / 3600) + "h ago";
    return Math.floor(diff / 86400) + "d ago";
  }
});

// DELETE POST
async function deletePost(id) {
  if (!confirm("Delete this post?")) return;
  const formData = new FormData();
  formData.append("post_id", id);

  const res = await fetch("delete_post.php", {
    method: "POST",
    body: formData,
  });

  const data = await res.json();
  if (data.success) {
    document.querySelector(`article[data-id='${id}']`).remove();
  } else {
    alert(data.message || "Failed to delete post");
  }
}

// LIKE POST
async function toggleLike(postId, btn) {
  const formData = new FormData();
  formData.append("post_id", postId);

  const res = await fetch("like_post.php", { method: "POST", body: formData });
  const data = await res.json();

  if (data.success) {
    btn.dataset.liked = data.liked;
    btn.innerHTML = `❤️ ${data.like_count}`;
  } else {
    alert(data.message || "Failed to like post");
  }
}

// TOGGLE COMMENTS
async function toggleComments(postId) {
  const section = document.getElementById(`comments-${postId}`);
  if (section.style.display === "none") {
    section.style.display = "block";
    loadComments(postId);
  } else {
    section.style.display = "none";
  }
}

// LOAD COMMENTS
async function loadComments(postId) {
  const res = await fetch(`fetch_comments.php?post_id=${postId}`);
  const data = await res.json();

  const list = document.querySelector(`#comments-${postId} .comments-list`);
  if (!data.length) {
    list.innerHTML = "<p class='no-comment'>No comments yet</p>";
    return;
  }

  list.innerHTML = data
    .map(
      (c) => `
    <div class="comment">
      <strong>@${c.username}</strong>: ${c.comment}
      <small>${timeAgo(c.created_at)}</small>
    </div>
  `
    )
    .join("");
}

// ADD COMMENT
async function addComment(postId) {
  const input = document.getElementById(`comment-input-${postId}`);
  const text = input.value.trim();
  if (!text) return;

  const formData = new FormData();
  formData.append("post_id", postId);
  formData.append("comment", text);

  const res = await fetch("add_comment.php", {
    method: "POST",
    body: formData,
  });
  const data = await res.json();

  if (data.success) {
    input.value = "";
    loadComments(postId);
  } else {
    alert(data.message || "Failed to comment");
  }
}

// REUSABLE timeAgo
function timeAgo(dateString) {
  const date = new Date(dateString);
  const diff = (Date.now() - date) / 1000;
  if (diff < 60) return "Just now";
  if (diff < 3600) return Math.floor(diff / 60) + "m ago";
  if (diff < 86400) return Math.floor(diff / 3600) + "h ago";
  return Math.floor(diff / 86400) + "d ago";
}
