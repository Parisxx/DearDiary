<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('De gebruikersnaam of e-mail is al in gebruik!'); window.location.href='index.php';</script>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, pfp) VALUES (:username, :email, :password, '')");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            echo "<script>alert('Registratie succesvol!'); window.location.href='login.php';</script>";
            exit();
        } else {
            echo "<script>alert('Er is een fout opgetreden bij het registreren'); window.location.href='index.php';</script>";
        }
    }
}
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
<div class="page-wrapper">
    <div class="image-wrapper">
      <img src="src/img/logo.png" alt="Dear Diary logo" class="top-image">
    </div>

    <div class="container">
        <h2>Register</h2>
        <form action="index.php" method="POST">
          <input type="text" name="username" placeholder="First name" required />
          <input type="email" name="email" placeholder="E-mail" required />
          <input type="password" name="password" placeholder="Password" required />
          <button type="submit">Register</button>
        </form>
        <div class="login-link">
          Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
  </div>
</body>

</html>