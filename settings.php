<?php
session_start();
require 'include/db.php'; // database connection

// Dummy user data for now
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // redirect if not logged in
    exit;
}
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dear Diary</title>
    <link rel="icon" type="image/x-icon" href="src/img/icon.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="navbar">
    <img src="src/img/logo.png" alt="Logo">
    <span class="username">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
    <div class="profile" onclick="toggleDropdown()">
        <img src="src/img/<?php echo !empty($_SESSION['pfp']) ? htmlspecialchars($_SESSION['pfp']) : 'default.png'; ?>" alt="Profile Picture">
        <div id="dropdown" class="dropdown hidden">
            <a href="dashboard.php">Home</a>
            <a href="settings.php">Settings</a>
        </div>
    </div>
</div>

  <div class="settings-container">
    <h2>Settings</h2>

  <div class="profile_section">
    <img src="src/img/<?php echo !empty($_SESSION['pfp']) ? htmlspecialchars($_SESSION['pfp']) : 'default.png'; ?>" alt="Profile Picture">
      <form action="include/upload_picture.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="picture" required>
        <button type="submit">Add profile picture</button>
      </form>
    <div class="profile_info">
      <form action="edit_name_email.php" method="POST">
        <p>Username</p>
        <input type="text" name="username" placeholder="<?= htmlspecialchars($user['username']) ?>" required>
        <button type="submit">Edit</button>
      </form>

      <form action="edit_email_email.php" method="POST">
        <p>E-mail</p>
        <input type="email" name="email" placeholder="<?= htmlspecialchars($user['email']) ?>" required>
        <button type="submit">Edit</button>
      </form>
    </div>

</div>

<div class="settings_container">
  <!-- Change Password -->
  <div class="password_section">
    <h3>Change password</h3>
    <form action="change_password.php" method="POST">
      <input type="password" name="new_password" placeholder="New password" required>
      <button type="submit">Change</button>
    </form>
  </div>

  <!-- Delete & Logout -->
  <div class="account_section">
    <form action="delete_account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
      <button type="submit">Delete Account</button>
    </form>
    <form action="logout.php" method="POST">
      <button type="submit">Logout</button>
    </form>
  </div>
</div>


  <script src="script.js"></script>
</body>
</html>
