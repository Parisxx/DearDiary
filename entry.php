<?php
include('db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$date = $_GET['date'] ?? null;

// Redirect if date not provided
if (!$date) {
    header("Location: dashboard.php");
    exit;
}

// Fetch entry for this user and date
$query = "
    SELECT e.content, m.name AS mood_name, m.color
    FROM entries e
    JOIN moods m ON e.mood_id = m.mood_id
    WHERE e.user_id = ? AND e.date = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id, $date]);
$entry = $stmt->fetch();

if (!$entry) {
    // Redirect if no entry exists
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dear Diary</title>
    <link rel="icon" type="image/x-icon" href="src/img/logo.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="navbar">

    <img src="src/img/logo.png" alt="Logo">
    <span class="username">Hi,  <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
    <div class="profile" onclick="toggleDropdown()">
    <img src="src/img/<?php echo !empty($_SESSION['pfp']) ? htmlspecialchars($_SESSION['pfp']) : 'default.png'; ?>" alt="Profile Picture">
        <div id="dropdown" class="dropdown hidden">
            <a href="dashboard.php">Home</a>
            <a href="settings.php">Settings</a>
        </div>
    </div>

</div>


<div class="date-mood-container">
    <h2><?php echo htmlspecialchars(date('l, F j, Y', strtotime($date))); ?></h2>
    <div class="mood-indicator">
        <img src="src/img/<?php echo strtolower($entry['mood_name']); ?>.png" alt="<?php echo htmlspecialchars($entry['mood_name']); ?>">
    </div>
</div>

<div class="entry-container">
    <div class="entry-content">
        <p><?php echo nl2br(htmlspecialchars($entry['content'])); ?></p>
    </div>
</div>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown');
        dropdown.classList.toggle('hidden'); // Toggle the visibility of the dropdown
    }
</script>

</body>
</html>