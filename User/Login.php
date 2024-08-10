<?php
// Start the session
session_start();

// Database connection settings
$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "user_db"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email and password from the form
    $Uemail = $_POST['email'];
    $Upassword = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $Uemail);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the email exists and password is correct
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        if (password_verify($Upassword, $user_data['password'])) {
            // Set session variables
            $_SESSION['email'] = $user_data['email'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['name'] = $user_data['name'];
            $_SESSION['surname'] = $user_data['surname'];
            $_SESSION['age'] = $user_data['age'];
            $_SESSION['birthday'] = $user_data['birthday'];
            $_SESSION['user_img'] = $user_data['user_img'];
            $_SESSION['address'] = $user_data['address'];

            // Close the statement and connection
            $stmt->close();
            $conn->close();

            // Redirect to a protected page
            header("Location: ../index.php");
            exit();
        } else {
            // Invalid credentials
            echo "<script>alert('Invalid email or password.');</script>";
            $_SESSION['error'] = "Invalid email or password.";
        }
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid email or password.');</script>";
        $_SESSION['error'] = "Invalid email or password.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
    header("Location: login.php");
    exit();
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
            <label for="email">Email or Phone:</label>
            <input type="text" id="email" name="email" required>
            
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
