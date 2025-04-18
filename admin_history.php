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
  <link rel="stylesheet" href="admin_history.css" />

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
    <a href="admin-dashboard.php">Home</a> &gt; History
  </div> 


<div class="content-box">
    <h3>The Legacy of OMPSA</h3>
    <p>
      Way back in the late twenties in 1949, the fondest ambition of several parents who believe in the 
      importance of catholic education was fulfilled when the late Fr. Alfredo Cordero together with his 
      co-founders opened the Our Mother of Perpetual Succor Academy. Seventy-six (76) pioneering young 
      men and women enrolled as first year and second year students. Fr. Alfredo Cordero was then the 
      first director with Mr. Esteban Abat as the principal.
    </p>
    <p>
      Upon marshalling the efforts and goodwill of some of the concerned people of Torrijos, Fr. Cordero 
      was able to form the board of trustees of the school, which was approved and adopted on May 17, 1952.
    </p>
    <p>
      Members of the board of said Stock Corporation were as follows:
      <ul>
        <li>Rev. Fr. Alfredo Cordero</li>
        <li>Mr. Jose Rosales</li>
        <li>Mr. Manuel Rosales</li>
        <li>Mr. Petronio Mataac</li>
      </ul>
    </p>
    <p>
      Rev. Fr. Cordero was authorized to solicit shares for the school and the 15 generous people favorably 
      responded with shares ranging from PhP 50 to PhP 400 each. Mr. Jose Rosales was elected as the treasurer 
      of the corporation.
    </p>
    <p>
      Considering the school as a catholic school, which prepares the youth to be more useful to their countrymen,
       and to show their love and affection to the school, the 31 shareholders of the stock corporation of OMPSA 
       donated their respective shares which was receive by the in charge of the school. Mother Ma. Andrea 
       Montejeros profoundly thanked them for their generosity on July 22, 1953.
    </p>
    <p>
      After 23 years of guiding and shepherding the school, Miss Imelda Quijano retired last June 2000. 
      Mrs. Enriqueta R. Fabul assumed the principalship. Bishop Jose F. Oliveros appointed Rev.Fr.Bienvenido Marticio
       who was then the school chaplain as the director of the school.
    </p>
    <p>
      After 23 years of guiding and shepherding the school, Miss Imelda Quijano retired last June 2000. 
      Mrs. Enriqueta R. Fabul assumed the principalship. Bishop Jose F. Oliveros appointed Rev.Fr.Bienvenido Marticio
       who was then the school chaplain as the director of the school.
    </p>
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

</script>

<script src="admin_history.js"></script>
</body>
</html>     