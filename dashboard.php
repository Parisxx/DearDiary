<?php
include('include/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

$user_id = $_SESSION['user_id']; 

$userQuery = $pdo->prepare("SELECT username, pfp FROM users WHERE user_id = ?");
$userQuery->execute([$user_id]);

if ($user = $userQuery->fetch()) {
    $_SESSION['username'] = $user['username'];
    $_SESSION['pfp'] = $user['pfp'];
}

$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('n');
$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');


$query = "
    SELECT e.date, m.color
    FROM entries e
    JOIN moods m ON e.mood_id = m.mood_id
    WHERE e.user_id = ? AND MONTH(e.date) = ? AND YEAR(e.date) = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id, $currentMonth, $currentYear]);
$moodData = [];
while ($row = $stmt->fetch()) {
    $moodData[$row['date']] = $row['color'];
}

$todayDate = date('Y-m-d');
$todayEntryStmt = $pdo->prepare("SELECT * FROM entries WHERE user_id = ? AND date = ?");
$todayEntryStmt->execute([$user_id, $todayDate]);
$hasEntryToday = $todayEntryStmt->rowCount() > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<span class="month-year"><?php echo date('F Y', strtotime("$currentYear-$currentMonth-01")); ?></span>
<div class="calendar-container">
    <div class="calendar" id="calendar"></div>
</div>

<div class="calendar-controls">
    <div onclick="navigateMonth('prev')" class="arrow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
    </div>

    <div onclick="navigateMonth('next')" class="arrow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </div>
</div>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}

const today = new Date();
let currentMonth = <?php echo $currentMonth; ?>;
let currentYear = <?php echo $currentYear; ?>;
const moodData = <?php echo json_encode($moodData); ?>;
const hasEntryToday = <?php echo $hasEntryToday ? 'true' : 'false'; ?>;

function navigateMonth(direction) {
    if (direction === 'prev') {
        currentMonth--;
        if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
    } else if (direction === 'next') {
        currentMonth++;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
    }
    window.location.href = `dashboard.php?month=${currentMonth}&year=${currentYear}`;
}

function generateCalendar() {
    const calendar = document.getElementById('calendar');
    const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

    calendar.innerHTML = '';

    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(currentYear, currentMonth - 1, day);
        const div = document.createElement('div');

        const formattedDate = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        let moodColor = moodData[formattedDate] || '#C3C3C3';

        if (day === today.getDate() && currentMonth === today.getMonth() + 1 && currentYear === today.getFullYear()) {
            div.classList.add('day', 'today');

            if (hasEntryToday && moodColor !== '#C3C3C3') {
                div.style.backgroundColor = moodColor;
            } else {
                div.style.backgroundColor = '#FFFFFF';
            }

            div.textContent = day;

            if (hasEntryToday) {
                div.onclick = () => window.location.href = `entry.php?date=${formattedDate}`;
            } else {
                div.onclick = () => window.location.href = 'write_entry.php';
            }

        } else if (date < today) {
            div.classList.add('day');
            div.style.backgroundColor = moodColor;
            div.textContent = day;

            if (moodColor !== '#C3C3C3') {
                div.onclick = () => window.location.href = `entry.php?date=${formattedDate}`;
            }

        } else {
            div.classList.add('day', 'future');
            div.style.backgroundColor = '#FFFFFF';
            div.textContent = day;
        }

        calendar.appendChild(div);
    }
}

generateCalendar();
</script>

</body>
</html>
