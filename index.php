<?php
// Include backend
include('include/register_backend.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DearDiary</title>
    <link rel="icon" type="image/x-icon" href="src/img/logo.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Page wrapper -->
<div class="page-wrapper">
    <!-- Logo image wrapper -->
    <div class="image-wrapper">
        <img src="src/img/logo.png" alt="Dear Diary logo" class="top-image">
    </div>

    <!-- Registration form container -->
    <div class="container">
        <h2>Register</h2>

        <!-- Registration form -->
        <form action="index.php" method="POST">
            <input type="text" name="username" placeholder="First name" required />
            <input type="email" name="email" placeholder="E-mail" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Register</button>
        </form>

        <!-- Login link -->
        <div class="login-link">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</div>

</body>
</html>
