<?php
// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email/phone and password from the form
    $emailPhone = $_POST['email-phone'];
    $password = $_POST['password'];

    // Example: Store user information in session (replace with database query in real application)
    $_SESSION['registered_users'][] = [
        'email-phone' => $emailPhone,
        'password' => $password
    ];

    // Redirect to a success page or login page
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="registerstyles.css">
</head>
<body>
    <div class="register-container">
        <form method="post">
            <h2>Register</h2>
            <label for="email-phone">Email or Phone:</label>
            <input type="text" id="email-phone" name="email-phone" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Register</button>
            
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</body>
</html>
