<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        echo "<script>alert('Inloggen succesvol!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        echo "<script>alert('Ongeldig e-mail of wachtwoord!'); window.location.href='login.php';</script>";
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DearDiary</title>
    <link rel="icon" type="image/x-icon" href="src/img/logo.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="page-wrapper">
    <div class="image-wrapper">
      <img src="src/img/logo.png" alt="Dear Diary logo" class="top-image">
    </div>

    <div class="container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="E-mail" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            Don't have an account? <a href="index.php">Register here</a>
        </div>
    </div>
</div>
</body>
</html>