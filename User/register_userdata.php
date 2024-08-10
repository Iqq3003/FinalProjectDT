<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $username = $_POST['username'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $age = $_POST['age'];
    $birthday = $_POST['birthday'];
    $phone = $_POST['phone'];
    $thai_id = $_POST['thai_id'];
    $address = $_POST['address'];
    $user_img = $_POST['user_img'];

    // ตรวจสอบว่าข้อมูลทุกฟิลด์ไม่ว่าง
    if(!empty($username) && !empty($name) && !empty($surname) && !empty($age) && !empty($birthday) && !empty($phone) && !empty($thai_id) && !empty($address) && !empty($user_img)) {
        // เตรียม statement สำหรับบันทึกข้อมูลลงในฐานข้อมูล
        $stmt = $conn->prepare("UPDATE users SET username=?, name=?, surname=?, age=?, birthday=?, phone=?, thai_id=?, address=?, user_img=? WHERE email=?");
        $stmt->bind_param("sssissssss", $username, $name, $surname, $age, $birthday, $phone, $thai_id, $address, $user_img, $_SESSION['email']);

        // บันทึกข้อมูล
        if ($stmt->execute()) {
            echo "<script>alert('Registration completed successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all fields.');</script>";
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Step 2</title>
    <link rel="stylesheet" href="registerstyles.css">
    <!-- ใส่ Firebase SDK -->
    <script type="module">
        // Import Firebase modules
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js";
        import { getStorage, ref, uploadBytesResumable, getDownloadURL } from "https://www.gstatic.com/firebasejs/10.12.5/firebase-storage.js";

        // Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyDEAjWq7e-rh4PRaV7BBLP3SLML2707wcM",
            authDomain: "iqq3003-4709b.firebaseapp.com",
            databaseURL: "https://iqq3003-4709b-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "iqq3003-4709b",
            storageBucket: "iqq3003-4709b.appspot.com",
            messagingSenderId: "937060870288",
            appId: "1:937060870288:web:611dd9824f30c3b3b79015",
            measurementId: "G-S6YNGMB6Z4"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const storage = getStorage(app);

        // Function to upload image to Firebase Storage
        window.uploadImage = function() {
            const file = document.getElementById('user_img_file').files[0];
            
            if (file) {
                const storageRef = ref(storage, 'images/' + file.name);
                const uploadTask = uploadBytesResumable(storageRef, file);

                uploadTask.on('state_changed', function(snapshot) {
                    // Observe state change events such as progress
                }, function(error) {
                    console.log(error);
                    alert('Image upload failed!');
                }, function() {
                    getDownloadURL(uploadTask.snapshot.ref).then(function(downloadURL) {
                        document.getElementById('user_img').value = downloadURL;
                        alert('Upload successful! Image URL: ' + downloadURL);
                    });
                });
            } else {
                alert('Please select an image to upload.');
            }
        }

        // Function to validate form fields
        function validateForm() {
            const username = document.getElementById('username').value.trim();
            const name = document.getElementById('name').value.trim();
            const surname = document.getElementById('surname').value.trim();
            const age = document.getElementById('age').value.trim();
            const birthday = document.getElementById('birthday').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const thai_id = document.getElementById('thai_id').value.trim();
            const address = document.getElementById('address').value.trim();
            const user_img = document.getElementById('user_img').value.trim();

            if (!username || !name || !surname || !age || !birthday || !phone || !thai_id || !address || !user_img) {
                alert('Please fill in all fields.');
                return false;
            }
            
            return true;
        }
    </script>
</head>
<body>
    <div class="register-step2-container">
        <h2>Register Step 2</h2>
        <form method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="surname">Surname:</label>
            <input type="text" id="surname" name="surname" required>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>

            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="thai_id">Thai ID:</label>
            <input type="text" id="thai_id" name="thai_id" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="user_img_file">Upload Image:</label>
            <input type="file" id="user_img_file" accept="image/*" required>
            <input type="hidden" id="user_img" name="user_img">

            <button type="button" onclick="uploadImage()">Upload Image</button>
            <button type="submit">Complete Registration</button>
        </form>
    </div>
</body>

</html>
