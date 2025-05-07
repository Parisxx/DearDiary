<?php
// Include database connection and start session
include('db.php');
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// Check if an entry for today already exists
$stmt = $pdo->prepare("SELECT * FROM entries WHERE user_id = ? AND date = ?");
$stmt->execute([$user_id, $today]);
$existingEntry = $stmt->fetch();

// Redirect to dashboard if an entry already exists
if ($existingEntry) {
    header("Location: dashboard.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mood_id = $_POST['mood_id'] ?? null;
    $content = $_POST['content'] ?? '';

    // Ensure both mood and content are provided
    if ($mood_id && $content) {
        // Insert new entry into the database
        $stmt = $pdo->prepare("INSERT INTO entries (user_id, date, mood_id, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $today, $mood_id, $content]);
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Please select a mood and write something.";  // Error if fields are empty
    }
}
