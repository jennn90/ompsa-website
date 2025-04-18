<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB config
$host = "localhost";
$user = "root";
$pass = "";
$db = "admin";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$passwordError = "";
$emailError = "";
$inputEmail = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputEmail = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $inputEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Save data to session
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_name'] = $user['name'];
                $_SESSION['admin_profile'] = $user['profile_pic'];

                // Redirect to dashboard
                header('Location: admin-dashboard.php');
                exit;
            } else {
                $passwordError = "Incorrect password.";
            }
        } else {
            $emailError = "Email not found.";
        }
        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="loginadmin.css">
    <style>
        input.error {
            border: 1px solid red;
            color: red;
        }
        input.error::placeholder {
            color: red;
        }
        .email-error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <form id="loginForm" method="POST" action="loginadmin.php">
        <input 
            type="email" 
            name="email" 
            placeholder="Email" 
            value="<?= htmlspecialchars($inputEmail) ?>" 
            required
        >
        <?php if (!empty($emailError)): ?>
            <div class="email-error"><?= htmlspecialchars($emailError) ?></div>
        <?php endif; ?>

        <input 
            type="password" 
            name="password" 
            placeholder="<?= $passwordError ? $passwordError : 'Password' ?>" 
            class="<?= $passwordError ? 'error' : '' ?>" 
            required
        >

        <button type="submit" class="login-btn">LOGIN</button>
    </form>
</div>

</body>
</html>
