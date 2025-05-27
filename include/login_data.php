<?php
// Include database connection
include('include/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Get user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify user and password
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        // Store user data in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        // Redirect on success
        echo "<script>alert('Inloggen succesvol!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        // Show error and redirect
        echo "<script>alert('Ongeldig e-mail of wachtwoord!'); window.location.href='login.php';</script>";
    }
}
?>