<?php
include('db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// Check if an entry already exists for today
$stmt = $pdo->prepare("SELECT * FROM entries WHERE user_id = ? AND date = ?");
$stmt->execute([$user_id, $today]);
$existingEntry = $stmt->fetch();

// Redirect to dashboard if entry already exists
if ($existingEntry) {
    header("Location: dashboard.php");
    exit;
}

// Handle form submission
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



// Get list of moods
$moodQuery = $pdo->query("SELECT mood_id, name FROM moods ORDER BY name");
$moods = $moodQuery->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta charset="UTF-8">
    <title>Dear Diary</title>
    <link rel="icon" type="image/x-icon" href="src/img/logo.png">
    <link rel="stylesheet" href="styles.css">
    <style>
        .entry-form {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            resize: vertical;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        select, button {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            width: 100%;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="entry-form">
    <h2>Write Entry for Today (<?php echo date('F j, Y'); ?>)</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="mood_id">How do you feel today?</label>
        <select name="mood_id" id="mood_id" required>
            <option value="">Select mood</option>
            <?php foreach ($moods as $mood): ?>
                <option value="<?php echo $mood['mood_id']; ?>"><?php echo htmlspecialchars($mood['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="content">Write about your day:</label>
        <textarea name="content" id="content" required></textarea>

        <button type="submit">Save Entry</button>
    </form>

    <a href="dashboard.php">← Back to Calendar</a>
</div>

</body>
</html>
