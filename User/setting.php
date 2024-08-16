<?php
session_start();
include 'connection.php'; // Include your database connection file

// Fetch current user data
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT username, name, surname, age, birthday, phone, user_job, user_img FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($username, $name, $surname, $age, $birthday, $phone, $user_job, $user_img);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fields = [];
    $params = [];
    $types = '';

    // Get additional details from the form and sanitize them
    if (!empty($_POST['username'])) {
        $fields[] = 'username=?';
        $params[] = htmlspecialchars($_POST['username']);
        $types .= 's';
    }
    if (!empty($_POST['name'])) {
        $fields[] = 'name=?';
        $params[] = htmlspecialchars($_POST['name']);
        $types .= 's';
    }
    if (!empty($_POST['surname'])) {
        $fields[] = 'surname=?';
        $params[] = htmlspecialchars($_POST['surname']);
        $types .= 's';
    }
    if (!empty($_POST['age'])) {
        $fields[] = 'age=?';
        $params[] = intval($_POST['age']);
        $types .= 'i';
    }
    if (!empty($_POST['birthday'])) {
        $fields[] = 'birthday=?';
        $params[] = htmlspecialchars($_POST['birthday']);
        $types .= 's';
    }
    if (!empty($_POST['phone'])) {
        $fields[] = 'phone=?';
        $params[] = htmlspecialchars($_POST['phone']);
        $types .= 's';
    }
    if (!empty($_POST['user_job'])) {
        $fields[] = 'user_job=?';
        $params[] = htmlspecialchars($_POST['user_job']);
        $types .= 's';
    }
    if (!empty($_POST['email'])) {
        $fields[] = 'email=?';
        $params[] = htmlspecialchars($_POST['email']);
        $types .= 's';
    }

    // Handle the file upload
    if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] == 0) {
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
                $fields[] = 'user_img=?';
                $params[] = $user_img;
                $types .= 's';
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
            }
        }
    }

    if (!empty($fields)) {
        $params[] = $email;
        $types .= 's';
        $stmt = $conn->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE email=?");
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo "<script>alert('Settings updated successfully!');</script>";
            header("Location: Profile.php");
        } else {
            echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('No fields to update.');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <script>
        function dropdownsFunc() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        window.onclick = function (event) {
            if (!event.target.matches('.dropbtn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="../IndexStylesheet.css">
</head>
<body>
<div class="topnav">
        <div class="navigationMenu">
            <a href="../">Home</a>
            <a href="../Woker/Search.php">Job</a>
            <a href="#section2">Company</a>
            <a href="#section3">About Us</a>
        </div>
        <div class="userMenu">
        
            <div class="dropdown">
                <?php if (isset($_SESSION['email'])): ?>
                    <a href="#user">LV.999</a>
                    <a onclick="dropdownsFunc()" class="dropbtn"><?php echo $_SESSION['username']; ?></a>
                    <img  class="userImg" src="../user_img/<?php echo $_SESSION['user_img']; ?>" alt="User Image" >
                    <div id="myDropdown" class="dropdown-content">
                        <a href="Profile.php">Profile</a>
                        <a href="messageslist.php">Message</a>
                        <a href="setting.php">Setting</a>
                        <a href="Logout.php">Log out</a>
                    </div>
                <?php else: ?>
                    <a href="../FinalProjectDT/User/Login.php">Login</a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    <form action="setting.php" method="post" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>"><br>

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>"><br>

        <label for="surname">Surname:</label>
        <input type="text" name="surname" id="surname" value="<?php echo htmlspecialchars($surname); ?>"><br>

        <label for="age">Age:</label>
        <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($age); ?>"><br>

        <label for="birthday">Birthday:</label>
        <input type="date" name="birthday" id="birthday" value="<?php echo htmlspecialchars($birthday); ?>"><br>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>"><br>

        <label for="user_job">Job:</label>
        <input type="text" name="user_job" id="user_job" value="<?php echo htmlspecialchars($user_job); ?>"><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>"><br>

        <label for="user_img">Profile Image:</label>
        <input type="file" name="user_img" id="user_img"><br>
        <img src="../user_img/<?php echo htmlspecialchars($user_img); ?>" alt="Profile Image" width="100"><br>

        <input type="submit" value="Save Settings">
    </form>
</body>
</html>
