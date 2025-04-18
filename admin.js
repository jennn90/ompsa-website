document.addEventListener("DOMContentLoaded", () => {
    const profileBtn = document.getElementById('profileBtn');
    const profilePanel = document.getElementById('profilePanel');
  
    profileBtn.addEventListener("click", () => {
      profilePanel.style.display = profilePanel.style.display === "block" ? "none" : "block";
    });
  
    document.getElementById("saveChangesBtn").addEventListener("click", () => {
      const newName = document.getElementById("usernameInput").value;
      if (newName) {
        document.getElementById("username").textContent = newName;
      }
  
      const fileInput = document.getElementById("profilePicInput");
      const profileImg = document.getElementById("profileImg");
      const profilePic = document.getElementById("profilePic");
  
      if (fileInput.files.length > 0) {
        const reader = new FileReader();
        reader.onload = function (e) {
          profileImg.src = e.target.result;
          profilePic.src = e.target.result;
        };
        reader.readAsDataURL(fileInput.files[0]);
      }
    });
  
    document.getElementById("logoutBtn").addEventListener("click", () => {
      window.location.href = "logout.php"; // or replace with your logout logic
    });
  });
  