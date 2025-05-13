<?php
// Include the backend
include('include/entry_backend.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dear Diary</title>
    <link rel="icon" type="image/x-icon" href="src/img/icon.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Navbar with user info and dropdown -->
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

<!-- Date and Mood Section -->
<div class="date-mood-container">
    <h2><?php echo htmlspecialchars(date('l, F j, Y', strtotime($date))); ?></h2>
    <div class="mood-indicator">
        <img src="src/img/<?php echo strtolower($entry['mood_name']); ?>.png" alt="<?php echo htmlspecialchars($entry['mood_name']); ?>">
    </div>
</div>

<!-- Entry Content Section -->
<div class="entry-container">
    <div class="entry-content">
        <p><?php echo nl2br(htmlspecialchars($entry['content'])); ?></p>
    </div>
</div>

<!-- Include script for dropdown functionality -->
<script src="script.js"></script>

</body>
</html>
