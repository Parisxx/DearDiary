<?php
// Include backend
include('include/login_backend.php');
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DearDiary</title>
    <link rel="icon" type="image/x-icon" href="src/img/icon.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Page wrapper -->
<div class="page-wrapper">
    <!-- Logo image wrapper -->
    <div class="image-wrapper">
        <img src="src/img/logo.png" alt="Dear Diary logo" class="top-image">
    </div>

    <!-- Login form container -->
    <div class="container">
        <h2>Login</h2>

        <!-- Login form -->
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="E-mail" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Login</button>
        </form>

        <!-- Register link -->
        <div class="register-link">
            Don't have an account? <a href="index.php">Register here</a>
        </div>
    </div>
</div>
</body>
</html>