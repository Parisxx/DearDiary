<?php
// Include database connection
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // User/email taken
        echo "<script>alert('De gebruikersnaam of e-mail is al in gebruik!'); window.location.href='index.php';</script>";
    } else {
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, pfp) VALUES (:username, :email, :password, '')");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            // Success
            echo "<script>alert('Registratie succesvol!'); window.location.href='login.php';</script>";
            exit();
        } else {
            // Error on insert
            echo "<script>alert('Er is een fout opgetreden bij het registreren'); window.location.href='index.php';</script>";
        }
    }
}
