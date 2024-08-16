<?php
session_start();
require_once 'connection.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า user ได้เข้าสู่ระบบแล้วหรือยัง
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];

// Prepare and execute the query to get the user ID
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$user_id = $user_data['id']; // Correctly retrieve the user_id

// ดึงข้อมูลผู้ใช้ที่เคยติดต่อกันจากฐานข้อมูล
$sql = "SELECT u.id, u.username, u.user_img AS profile_image, 
               (SELECT message FROM messages WHERE id = c.last_message_id) AS last_message, 
               (SELECT sent_at FROM messages WHERE id = c.last_message_id) AS last_message_time
        FROM users u
        JOIN conversations c ON (u.id = c.user1_id OR u.id = c.user2_id)
        WHERE (c.user1_id = ? OR c.user2_id = ?)
        AND u.id != ?
        GROUP BY u.id, u.username, u.user_img, c.last_message_id
        ORDER BY last_message_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages List</title>
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

<div id="home" class="section">
    <h1>Messages</h1>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <a href="messages.php?receiver_id=<?php echo $row['id']; ?>">
                    <img src="../user_img/<?php echo htmlspecialchars($row['profile_image']); ?>" alt="<?php echo htmlspecialchars($row['username']); ?>" style="width: 50px; height: 50px;">
                    <strong><?php echo htmlspecialchars($row['username']); ?></strong><br>
                    <span><?php echo htmlspecialchars($row['last_message']); ?></span><br>
                    <span><?php echo htmlspecialchars($row['last_message_time']); ?></span>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
</body>
</html>
