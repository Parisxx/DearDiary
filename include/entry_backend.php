<?php
// Include database connection
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

// Redirect if no entry exists
if (!$entry) {
    header("Location: dashboard.php");
    exit;
}