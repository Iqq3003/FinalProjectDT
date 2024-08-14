<?php
// Start the session
session_start();

// Database connection
$servername = "localhost"; // or your database server
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "user_db"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get job ID from the URL
$workId = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch job details from the database
$sql = "SELECT workdata.work_id, workdata.work_N, workdata.work_img, workdata.work_description, users.username 
        FROM workdata 
        JOIN users ON workdata.user_id = users.id 
        WHERE workdata.work_id = '" . $conn->real_escape_string($workId) . "'";
$result = $conn->query($sql);

if ($result === false) {
    die("SQL Error: " . $conn->error);
}

// Fetch job details
$jobDetails = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Detail</title>
    <link rel="stylesheet" href="SearchStyles.css">
</head>
<body>
    <div class="topnav">
        <div class="navigationMenu">
            <a href="../index.php">Home</a>
            <a class="active" href="#section1">Job</a>
            <a href="#section2">Company</a>
            <a href="#section3">About Us</a>
        </div>
        <div class="userMenu">
            <div class="dropdown">
                <?php if (isset($_SESSION['email'])): ?>
                    <a href="#user">LV.999</a>
                    <a onclick="dropdownsFunc()" class="dropbtn"><?php echo $_SESSION['username']; ?></a>
                    <img class="userImg" src="../user_img/<?php echo $_SESSION['user_img']; ?>" alt="User Image">
                    <div id="myDropdown" class="dropdown-content">
                        <a href="../User/Profile.php">Profile</a>
                        <a href="#about">Message</a>
                        <a href="#contact">Setting</a>
                        <a href="../User/Logout.php">Log out</a>
                    </div>
                <?php else: ?>
                    <a href="../User/Login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="main">
        <section class="job-detail">
            <?php if ($jobDetails): ?>
                <h1><?php echo htmlspecialchars($jobDetails["work_N"]); ?></h1>
                <img src="../work_img/<?php echo htmlspecialchars($jobDetails["work_img"]); ?>" alt="Job Image">
                <p><strong>Posted by:</strong> <?php echo htmlspecialchars($jobDetails["username"]); ?></p>
                <p><strong>Description:</strong></p>
                <p><?php echo htmlspecialchars($jobDetails["work_description"]); ?></p>
            <?php else: ?>
                <p>Job details not found.</p>
            <?php endif; ?>
        </section>
    </div>
    <div class="footer">
        <p>Footer Content</p>
    </div>
</body>
</html>
