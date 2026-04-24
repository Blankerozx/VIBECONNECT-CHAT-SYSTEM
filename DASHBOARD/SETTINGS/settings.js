document.addEventListener("DOMContentLoaded", () => {
  const uploadBtn = document.getElementById("uploadBtn");
  const profilePicInput = document.getElementById("profilePicInput");
  const saveProfileBtn = document.getElementById("saveProfileBtn");
  const updatePasswordBtn = document.getElementById("updatePasswordBtn");
  const status = document.getElementById("statusMessage");

  // Upload new profile picture
  uploadBtn.addEventListener("click", async () => {
    const file = profilePicInput.files[0];
    if (!file) return alert("Select an image first.");

    const formData = new FormData();
    formData.append("profile_pic", file);

    const res = await fetch("update_profile_pic.php", {
      method: "POST",
      body: formData
    });

    const data = await res.json();
    status.textContent = data.message;
    status.style.color = data.success ? "green" : "red";

    if (data.success) {
      document.getElementById("profilePreview").src = data.newPath;
    }
  });

  // Update username & email
  saveProfileBtn.addEventListener("click", async () => {
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();

    const res = await fetch("update_profile_info.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, email })
    });

    const data = await res.json();
    status.textContent = data.message;
    status.style.color = data.success ? "green" : "red";
  });

  // Change password
  updatePasswordBtn.addEventListener("click", async () => {
    const current = document.getElementById("currentPassword").value;
    const newPass = document.getElementById("newPassword").value;
    const confirm = document.getElementById("confirmPassword").value;

    if (newPass !== confirm) {
      status.textContent = "New passwords do not match.";
      status.style.color = "red";
      return;
    }

    const res = await fetch("update_password.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ current, newPass })
    });

    const data = await res.json();
    status.textContent = data.message;
    status.style.color = data.success ? "green" : "red";
  });
});
