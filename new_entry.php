<?php
// Include backend
include('include/new_entry_data.php');

// Fetch available moods from the database
$moodQuery = $pdo->query("SELECT mood_id, name FROM moods WHERE mood_id BETWEEN 1 AND 7 ORDER BY mood_id");
$moods = $moodQuery->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dear Diary</title>
    <link rel="icon" type="image/x-icon" href="src/img/icon.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Navbar Section -->
    <div class="navbar">
        <img src="src/img/logo.png" alt="Logo">
        <span class="username">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        <div class="profile" onclick="toggleDropdown()">
            <img src="src/img/<?php echo !empty($_SESSION['pfp']) ? htmlspecialchars($_SESSION['pfp']) : 'default.png'; ?>" alt="Profile Picture">
            <!-- Dropdown Menu -->
            <div id="dropdown" class="dropdown hidden">
                <a href="dashboard.php">Home</a>
                <a href="settings.php">Settings</a>
            </div>
        </div>
    </div>

    <!-- Date Display -->
    <div class="date-mood-container">
        <h2><?php echo date('l, F j, Y'); ?></h2>
    </div>

    <!-- Entry Form Section -->
    <div class="entry-form">
        <form id="entryForm" method="POST">
            <!-- Textarea for Diary Entry -->
            <div class="textarea-wrapper">
                <textarea placeholder="Dear Diary..." name="content" id="content" required></textarea>
            </div>

            <!-- Hidden Field for Mood ID -->
            <input type="hidden" name="mood_id" id="mood_id">
        </form>
    </div>

    <!-- Mood Gallery Section -->
    <div class="mood-gallery">
        <?php foreach ($moods as $mood): ?>
            <img src="src/img/<?php echo htmlspecialchars($mood['name']); ?>.png"
                 alt="<?php echo htmlspecialchars($mood['name']); ?>"
                 onclick="selectMood('<?php echo $mood['mood_id']; ?>')">
        <?php endforeach; ?>
    </div>

    <!-- Link to External JS File -->
    <script src="script.js"></script>

</body>
</html>
