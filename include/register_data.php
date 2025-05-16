<?php
// Include database connection
include('db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email or username already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // If email or username already exists
    if ($stmt->rowCount() > 0) {
        echo "<script>alert('De gebruikersnaam of e-mail is al in gebruik!'); window.location.href='index.php';</script>";
    } else {
        // Insert new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, pfp) VALUES (:username, :email, :password, '')");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        // Execute the insert query
        if ($stmt->execute()) {
            echo "<script>alert('Registratie succesvol!'); window.location.href='login.php';</script>";
            exit();
        } else {
            echo "<script>alert('Er is een fout opgetreden bij het registreren'); window.location.href='index.php';</script>";
        }
    }
}