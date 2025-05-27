<?php
// Include the database connection
include('include/db.php');

// Start the session to use session variables
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID from the session

// Fetch username and profile picture from the database
$userQuery = $pdo->prepare("SELECT username, pfp FROM users WHERE user_id = ?");
$userQuery->execute([$user_id]);

if ($user = $userQuery->fetch()) {
    $_SESSION['username'] = $user['username'];
    $_SESSION['pfp'] = $user['pfp'];
}

// Get current month and year from URL or use current date
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('n');
$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Get mood color data for each entry in the current month
$query = "
    SELECT e.date, m.color
    FROM entries e
    JOIN moods m ON e.mood_id = m.mood_id
    WHERE e.user_id = ? AND MONTH(e.date) = ? AND YEAR(e.date) = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id, $currentMonth, $currentYear]);

// Store mood color per day
$moodData = [];
while ($row = $stmt->fetch()) {
    $moodData[$row['date']] = $row['color'];
}

// Get how many times each mood was used this month
$moodDistributionQuery = "
    SELECT m.name, m.color, COUNT(*) as count
    FROM entries e
    JOIN moods m ON e.mood_id = m.mood_id
    WHERE e.user_id = ? AND MONTH(e.date) = ? AND YEAR(e.date) = ?
    GROUP BY m.mood_id
    ORDER BY m.mood_id
";
$moodDistributionStmt = $pdo->prepare($moodDistributionQuery);
$moodDistributionStmt->execute([$user_id, $currentMonth, $currentYear]);
$moodDistribution = $moodDistributionStmt->fetchAll(PDO::FETCH_ASSOC);

// Count total number of mood entries
$totalMoods = array_sum(array_column($moodDistribution, 'count'));  

// Calculate percentage for each mood
foreach ($moodDistribution as &$mood) {
    $mood['percentage'] = $totalMoods > 0 ? ($mood['count'] / $totalMoods) * 100 : 0;
}
unset($mood);

// Check if the user has already written an entry today
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
    <link rel="icon" type="image/x-icon" href="src/img/icon.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Navigation bar with logo, greeting and profile picture -->
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

<!-- Mood bar shows percentage of each mood this month -->
<div class="horizontal-bar">
    <?php foreach ($moodDistribution as $mood): ?>
        <div style="background-color: <?php echo htmlspecialchars($mood['color']); ?>; width: <?php echo $mood['percentage']; ?>%;"></div>
    <?php endforeach; ?>
</div>

<!-- Show current month and year -->
<span class="month-year"><?php echo date('F Y', strtotime("$currentYear-$currentMonth-01")); ?></span>

<!-- Calendar container -->
<div class="calendar-container">
    <div class="calendar" id="calendar"></div>
</div>

<!-- Buttons to switch months -->
<div class="calendar-controls">
    <div onclick="navigateMonth('prev')" class="arrow">
        <!-- Left arrow SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
    </div>

    <div onclick="navigateMonth('next')" class="arrow">
        <!-- Right arrow SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </div>
</div>

<script src="script.js"></script>

<script>
// Prepare data from PHP for use in JavaScript
const today = new Date();
let currentMonth = <?php echo $currentMonth; ?>;
let currentYear = <?php echo $currentYear; ?>;
const moodData = <?php echo json_encode($moodData); ?>;
const hasEntryToday = <?php echo $hasEntryToday ? 'true' : 'false'; ?>;

// Function to go to previous or next month
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
    // Reload page with new month and year
    window.location.href = `dashboard.php?month=${currentMonth}&year=${currentYear}`;
}

// Generate calendar with mood colors and clickable days
function generateCalendar() {
    const calendar = document.getElementById('calendar');
    const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

    calendar.innerHTML = ''; // Clear previous calendar

    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(currentYear, currentMonth - 1, day);
        const div = document.createElement('div');

        const formattedDate = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        let moodColor = moodData[formattedDate] || '#C3C3C3'; // Default gray color

        if (day === today.getDate() && currentMonth === today.getMonth() + 1 && currentYear === today.getFullYear()) {
            // Today
            div.classList.add('day', 'today');

            if (hasEntryToday && moodColor !== '#C3C3C3') {
                div.style.backgroundColor = moodColor;
            } else {
                div.style.backgroundColor = '#FFFFFF';
                div.classList.add('pulse-ring'); 
            }

            div.textContent = day;

            // Redirect to entry page
            if (hasEntryToday) {
                div.onclick = () => window.location.href = `entry.php?date=${formattedDate}`;
            } else {
                div.onclick = () => window.location.href = 'new_entry.php';
            }

        } else if (date < today) {
            // Past days
            div.classList.add('day');
            div.style.backgroundColor = moodColor;
            div.textContent = day;

            if (moodColor !== '#C3C3C3') {
                div.onclick = () => window.location.href = `entry.php?date=${formattedDate}`;
            }

        } else {
            // Future days
            div.classList.add('day', 'future');
            div.style.backgroundColor = '#FFFFFF';
            div.textContent = day;
        }

        calendar.appendChild(div);
    }
}

// Run calendar generation on page load
generateCalendar();
</script>

</body>
</html>
