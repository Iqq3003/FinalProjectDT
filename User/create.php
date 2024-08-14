<?php
session_start();

// connection.php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure the session email is set
    if (isset($_SESSION['email'])) {
        // Get additional details from the form and sanitize them
        $work_N = isset($_POST['Job']) ? htmlspecialchars($_POST['Job']) : '';
        
        // Retrieve user_id from the "users" table
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt === false) {
            die("Error preparing SQL statement: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        // Handle the file upload
        if (isset($_FILES['work_img'])) {
            $target_dir = "../work_img/";
            $target_file = $target_dir . basename($_FILES["work_img"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if file is an image
            if ($_FILES["work_img"]["tmp_name"] && getimagesize($_FILES["work_img"]["tmp_name"]) === false) {
                echo "<script>alert('File is not an image.');</script>";
                $uploadOk = 0;
            }

            // Check file size (5MB maximum)
            if ($_FILES["work_img"]["size"] > 5000000) {
                echo "<script>alert('Sorry, your file is too large.');</script>";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "<script>alert('Sorry, your file was not uploaded.');</script>";
            } else {
                if (move_uploaded_file($_FILES["work_img"]["tmp_name"], $target_file)) {
                    // File is uploaded successfully
                    $work_img = htmlspecialchars(basename($_FILES["work_img"]["name"]));

                    if (!empty($work_N) && !empty($work_img)) {
                        // Prepare the INSERT statement
                        $stmt = $conn->prepare("INSERT INTO workdata (user_id, work_N, work_img) VALUES (?, ?, ?)");
                        if ($stmt === false) {
                            die("Error preparing SQL statement: " . htmlspecialchars($conn->error));
                        }
                        // Bind the parameters
                        $stmt->bind_param("sss", $user_id, $work_N, $work_img);
                        
                        // Execute the statement and check for success
                        if ($stmt->execute()) {
                            echo "<script>alert('Registration completed successfully!');</script>";
                        } else {
                            echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
                        }
                        
                        // Close the statement
                        $stmt->close();
                    } else {
                        echo "<script>alert('Please fill in all fields.');</script>";
                    }
                    
                } else {
                    echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
                }
            }
        } else {
            echo "<script>alert('No file uploaded.');</script>";
        }
    } else {
        echo "<script>alert('User not logged in.');</script>";
    }

    $conn->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Job</title>
    <link rel="stylesheet" href="registerstyles.css">
</head>
<body>
    <div class="register-container">
        <form method="post" enctype="multipart/form-data">
            <h2>Register Job</h2>

            <input type="text" id="Job" name="Job" placeholder="Job you want" required>
            
            <label for="work_img">Add Image:</label>
            <input type="file" id="work_img" name="work_img" accept="image/*">

            <button type="submit">Complete Registration</button>
        </form>
    </div>
</body>
</html>
