<?php 
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: loginadmin.php');
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "admin";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$updateSuccess = "";
$updateError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newName = $_POST['name'] ?? '';
    $profilePic = $_FILES['profile_pic'] ?? null;
    $adminId = $_SESSION['admin_id'];

    if (!empty($newName)) {
        $stmt = $conn->prepare("UPDATE admin SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $newName, $adminId);
        if ($stmt->execute()) {
            $_SESSION['admin_name'] = $newName;
            $updateSuccess = "Name updated successfully!";
        } else {
            $updateError = "Failed to update name.";
        }
        $stmt->close();
    }

    if ($profilePic && $profilePic['error'] === 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $fileName = basename($profilePic["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($profilePic["tmp_name"], $targetFile)) {
                $stmt = $conn->prepare("UPDATE admin SET profile_pic = ? WHERE id = ?");
                $stmt->bind_param("si", $targetFile, $adminId);
                if ($stmt->execute()) {
                    $_SESSION['admin_profile'] = $targetFile;
                    $updateSuccess .= " Profile picture updated!";
                }
                $stmt->close();
            } else {
                $updateError = "Error uploading image.";
            }
        } else {
            $updateError = "Invalid file type.";
        }
    }

    header("Location: admin-dashboard.php?update=success");
    exit;
}

$conn->close();
?>

<!-- START HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>OMPSA School Portal</title>
  <link rel="stylesheet" href="admin_faculty.css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<input type="checkbox" id="toggle">
<label class="hamburger" for="toggle">
  <span></span><span></span><span></span>
</label>

<div class="sidebar">
  <a href="admin-dashboard.php"><i class="fas fa-house"></i> Home</a>
  <a href="photos.html"><i class="fas fa-image"></i> Photos</a>
  <a href="videos.html"><i class="fas fa-video"></i> Videos</a>
  <a href="prayers.html"><i class="fas fa-hands-praying"></i> Prayers</a>
</div>

<div class="main-content">
  <header>
    <h1>
      <img src="images/logo ompsa.png" alt="Logo" width="40" height="40">
      OUR MOTHER OF PERPETUAL SUCCOR ACADEMY
    </h1>

    <div class="profile" id="profileBtn">
      <img src="<?= htmlspecialchars($_SESSION['admin_profile'] ?? 'uploads/default.png') ?>" id="profileImg" alt="Profile">
      <span><?= htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_email'] ?? '') ?></span>
    </div>

    <div id="profilePanel" class="profile-panel">
      <div class="panel-header"><h3>Account</h3></div>

      <form class="panel-content" method="POST" enctype="multipart/form-data" action="admin-dashboard.php">
        <img 
          id="profilePic" 
          src="<?= htmlspecialchars($_SESSION['admin_profile'] ?? 'uploads/default.png') ?>" 
          width="80" height="80" 
          style="border-radius: 50%; margin-bottom: 10px;" 
          alt="Profile Picture"
        >

        <input type="file" name="profile_pic" id="profilePicInput" accept="image/*">
        <input 
          type="text" 
          name="name" 
          id="usernameInput" 
          placeholder="Your name" 
          value="<?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?>" 
          required
        >

        <button type="submit" id="saveChangesBtn">Save</button>
      </form>

      <form action="logout.php" method="post">
        <button type="submit" id="logoutBtn">Logout</button>
      </form>

      <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
        <p class="success">Profile updated successfully!</p>
      <?php elseif (!empty($updateError)): ?>
        <p class="error"><?= htmlspecialchars($updateError) ?></p>
      <?php endif; ?>
    </div>
  </header>

  <div class="content">
    <a href="admin-dashboard.php">Home</a> &gt; Faculty & Staff
  </div> 

    <div class="faculty-section">
      <h3>Administration</h3>
      <div class="faculty-container" id="admin-section"></div>
      <button class="add-btn" onclick="addCard('admin-section')">Add Person</button>
    </div>

    <div class="faculty-section">
      <h3>Faculty</h3>
      <div class="faculty-container" id="faculty-section"></div>
      <button class="add-btn" onclick="addCard('faculty-section')">Add Person</button>
    </div>

    <div class="faculty-section">
      <h3>Staff</h3>
      <div class="faculty-container" id="staff-section"></div>
      <button class="add-btn" onclick="addCard('staff-section')">Add Person</button>
    </div>

    <div class="faculty-section">
      <h3>Former Director and Principals</h3>
      <div class="faculty-container" id="former-section"></div>
      <button class="add-btn" onclick="addCard('former-section')">Add Person</button>
    </div>
  </div>
</div>

<!-- JS for image preview -->
<script>
  const profilePicInput = document.getElementById('profilePicInput');
  const profileImg = document.getElementById('profilePic');

  document.getElementById('profileBtn').addEventListener('click', () => {
  document.getElementById('profilePanel').classList.toggle('show');
});

  profilePicInput?.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        profileImg.src = e.target.result;
      }
      reader.readAsDataURL(file);
    }
  });

  function addCard(sectionId) {
    const container = document.getElementById(sectionId);
    if (!container) {
      alert(`No container found with ID: ${sectionId}`);
      return;
    }

    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `
      <img src="https://via.placeholder.com/100" onclick="this.nextElementSibling.click()" alt="New Person">
      <input type="file" accept="image/*" onchange="changePicture(this)">
      <div class="name" contenteditable="true">New Name</div>
      <div class="position" contenteditable="true">Position</div>
      <div class="btn-container">
        <button class="edit-btn" onclick="toggleEdit(this)">Save</button>
        <button class="delete-btn" onclick="this.parentElement.parentElement.remove()">Delete</button>
      </div>
    `;
    container.appendChild(card);
  }

  function changePicture(input) {
    const file = input.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        input.previousElementSibling.src = e.target.result;
      }
      reader.readAsDataURL(file);
    }
  }

  function toggleEdit(btn) {
    const card = btn.closest('.card');
    const name = card.querySelector('.name');
    const position = card.querySelector('.position');
    if (name.isContentEditable) {
      name.contentEditable = "false";
      position.contentEditable = "false";
      btn.textContent = "Edit";
    } else {
      name.contentEditable = "true";
      position.contentEditable = "true";
      btn.textContent = "Save";
    }
  }
</script>

<script src="admin.js"></script>
</body>
</html>
