<?php
// Include database connection
include('include/db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query to check user credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Fetch user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists and if the password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Start the session for the user
        session_start();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        // Redirect to the dashboard
        echo "<script>alert('Inloggen succesvol!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        // If credentials are invalid
        echo "<script>alert('Ongeldig e-mail of wachtwoord!'); window.location.href='login.php';</script>";
    }
}
?>