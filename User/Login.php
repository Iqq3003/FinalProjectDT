<?php
// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email/phone and password from the form
    $emailPhone = $_POST['email-phone'];
    $password = $_POST['password'];

    // Example hardcoded credentials (replace with database query in real application)
    $validEmailPhone = "a";
    $validPassword = "123456";

    // Check if the credentials are valid
    if ($emailPhone == $validEmailPhone && $password == $validPassword) {
        // Set session variables
        $_SESSION['email-phone'] = $emailPhone;
        // Redirect to a protected page
        header("Location: ../index.html");
        exit();
    } else {
        // Invalid credentials
        $_SESSION['error'] = "Invalid email/phone or password.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="lopginstyles.css">
</head>
<body>
    <div class="login-container">
        <form method="post">
            <h2>Login</h2>
            <label for="email-phone">Email or Phone:</label>
            <input type="text" id="email-phone" name="email-phone" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <?php
            if (isset($_SESSION['error'])) {
                $error = $_SESSION['error'];
                echo "<p style='color: red;'>$error</p>";
                unset($_SESSION['error']); // Clear the error message
            }
            ?>
            <button type="submit">Login</button>
            
            <div class="social-login">
                <button type="button" onclick="loginWithGoogle()">Login with Google</button>
                <button type="button" onclick="loginWithFacebook()">Login with Facebook</button>
            </div>
            
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>

    <script>
        function loginWithGoogle() {
            // Add your Google login logic here
            alert("Google login not implemented yet.");
        }

        function loginWithFacebook() {
            // Add your Facebook login logic here
            alert("Facebook login not implemented yet.");
        }
    </script>
</body>
</html>
