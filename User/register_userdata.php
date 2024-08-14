<?php
session_start();
include('connection.php');

// ตรวจสอบว่ามีการกรอก email และ password มาก่อนหรือไม่
if (!isset($_SESSION['email'])) {
    echo("<script>alert('Abnormal Access')</script>");
    header("Location: Login.php");
    exit();
}

// Database connection settings



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get additional details from the form and sanitize them
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $surname = isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : '';
    $age = isset($_POST['age']) ? intval($_POST['age']) : '';
    $birthday = isset($_POST['birthday']) ? htmlspecialchars($_POST['birthday']) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
    $Thai_id = isset($_POST['Thai_id']) ? htmlspecialchars($_POST['Thai_id']) : '';
    $address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
    
    // Handle the file upload
    if (isset($_FILES['user_img'])) {
        $target_dir = "../user_img/";
        $target_file = $target_dir . basename($_FILES["user_img"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an image
        if ($_FILES["user_img"]["tmp_name"] && getimagesize($_FILES["user_img"]["tmp_name"]) === false) {
            echo "<script>alert('File is not an image.');</script>";
            $uploadOk = 0;
        }

        // Check file size (5MB maximum)
        if ($_FILES["user_img"]["size"] > 5000000) {
            echo "<script>alert('Sorry, your file is too large.');</script>";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "<script>alert('Sorry, your file was not uploaded.');</script>";
        } else {
            if (move_uploaded_file($_FILES["user_img"]["tmp_name"], $target_file)) {
                // File is uploaded successfully
                $user_img = htmlspecialchars(basename($_FILES["user_img"]["name"]));

                // Prepare and execute the statement to update user info
                if (!empty($username) && !empty($name) && !empty($surname) && !empty($age) && !empty($birthday) && !empty($phone) && !empty($Thai_id) && !empty($address) && !empty($user_img)) {
                    $stmt = $conn->prepare("UPDATE users SET username=?, name=?, surname=?, age=?, birthday=?, phone=?, thai_id=?, address=?, user_img=? WHERE email=?");
                    $stmt->bind_param("sssissssss", $username, $name, $surname, $age, $birthday, $phone, $Thai_id, $address, $user_img, $_SESSION['email']);
            
                    if ($stmt->execute()) {
                        echo "<script>alert('Registration completed successfully!');</script>";
                    } else {
                        echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
                    }
            
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

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Step 2</title>
    <link rel="stylesheet" href="registerstyles.css">
</head>
<body>
    <div class="register-container">
        <form method="post" enctype="multipart/form-data">
            <h2>Register Personal Data</h2>

            <input type="text" id="username" name="username" placeholder="username" required>

            <input type="text" id="name" name="name" placeholder="name" required>

            <input type="text" id="surname" name="surname" placeholder="surname" required>

            <input type="number" id="age" name="age" placeholder="age" required>

            <input type="date" id="birthday" name="birthday"  placeholder="birthday" required>
 
            <input type="text" id="phone" name="phone" placeholder="Phone number" required>

            <input type="text" id="Thai_id" name="Thai_id" placeholder="Thai ID" required>

            <textarea id="address" name="address" placeholder="Address" required></textarea>
            

            <label for="user_img">User Image:</label>
            <input type="file" id="user_img" name="user_img" accept="image/*">

            <button type="submit">Complete Registration</button>
        </form>
    </div>
</body>
</html>
