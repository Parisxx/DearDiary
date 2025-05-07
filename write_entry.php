<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

$stmt = $pdo->prepare("SELECT * FROM entries WHERE user_id = ? AND date = ?");
$stmt->execute([$user_id, $today]);
$existingEntry = $stmt->fetch();

if ($existingEntry) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mood_id = $_POST['mood_id'] ?? null;
    $content = $_POST['content'] ?? '';

    if ($mood_id && $content) {
        $stmt = $pdo->prepare("INSERT INTO entries (user_id, date, mood_id, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $today, $mood_id, $content]);
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Please select a mood and write something.";
    }
}

$moodQuery = $pdo->query("SELECT mood_id, name FROM moods WHERE mood_id BETWEEN 1 AND 7 ORDER BY mood_id");
$moods = $moodQuery->fetchAll();
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
    <span class="username">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
    <div class="profile" onclick="toggleDropdown()">
    <img src="src/img/<?php echo !empty($_SESSION['pfp']) ? htmlspecialchars($_SESSION['pfp']) : 'default.png'; ?>" alt="Profile Picture">
        <div id="dropdown" class="dropdown hidden">
            <a href="dashboard.php">Home</a>
            <a href="settings.php">Settings</a>
        </div>
    </div>
</div>

<div class="date-mood-container">
    <h2><?php echo date('l, F j, Y'); ?></h2>
</div>

<div class="entry-form">
    <form id="entryForm" method="POST">
        <div class="textarea-wrapper">
            <textarea placeholder="Dear Diary..." name="content" id="content" required></textarea>
        </div>

        <input type="hidden" name="mood_id" id="mood_id">
        <button type="submit" class="hidden">Save Entry</button>
    </form>
</div>

<div class="mood-gallery">
    <?php foreach ($moods as $mood): ?>
        <img src="src/img/<?php echo htmlspecialchars($mood['name']); ?>.png"
             alt="<?php echo htmlspecialchars($mood['name']); ?>"
             onclick="selectMood('<?php echo $mood['mood_id']; ?>')">
    <?php endforeach; ?>
</div>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown');
        dropdown.classList.toggle('hidden');
    }

    function selectMood(moodId) {
        const content = document.getElementById('content').value.trim();
        if (!content) {
            alert("Please write something before selecting your mood.");
            return;
        }

        document.getElementById('mood_id').value = moodId;
        document.getElementById('entryForm').submit();
    }
</script>

</body>
</html>
