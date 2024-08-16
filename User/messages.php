<?php
session_start();
include 'connection.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

// Prepare and execute the query to get the user ID
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if ($user_data) {
    $user_id = $user_data['id']; // Correctly retrieve the user_id
} else {
    // Handle the case where the user is not found
    echo "User not found.";
    exit();
}

$other_user_id = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;

// Check if other_user_id is set correctly
if ($other_user_id == 0) {
    echo "Invalid receiver ID.";
    exit();
}

// Fetch messages between the two users
$sql = "SELECT m.message, m.sent_at, u.username AS sender
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) 
        OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.sent_at";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $other_user_id, $other_user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the username of the other user
$sql_user = "SELECT username FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $other_user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$other_user_data = $result_user->fetch_assoc();

if ($other_user_data) {
    $other_username = htmlspecialchars($other_user_data['username']); // Corrected this line
} else {
    // Handle the case where the other user is not found
    $other_username = "Unknown User";
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (!empty($message)) { // Check if message is not empty
        // Insert message into database
        $sql_insert = "INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iis", $user_id, $other_user_id, $message);

        if ($stmt_insert->execute()) {
            $last_message_id = $conn->insert_id; // Get the ID of the last inserted message
            
            // Check if conversation record exists
            $sql_convo_check = "SELECT * FROM conversations WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)";
            $stmt_convo_check = $conn->prepare($sql_convo_check);
            $stmt_convo_check->bind_param("iiii", $user_id, $other_user_id, $other_user_id, $user_id);
            $stmt_convo_check->execute();
            $result_convo_check = $stmt_convo_check->get_result();

            if ($result_convo_check->num_rows == 0) {
                // Insert new conversation record
                $sql_convo_insert = "INSERT INTO conversations (user1_id, user2_id, last_message_id) VALUES (?, ?, ?)";
                $stmt_convo_insert = $conn->prepare($sql_convo_insert);
                $stmt_convo_insert->bind_param("iii", $user_id, $other_user_id, $last_message_id);
                $stmt_convo_insert->execute();
            } else {
                // Update existing conversation record with the last message ID
                $sql_convo_update = "UPDATE conversations SET last_message_id = ? WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)";
                $stmt_convo_update = $conn->prepare($sql_convo_update);
                $stmt_convo_update->bind_param("iiiii", $last_message_id, $user_id, $other_user_id, $other_user_id, $user_id);
                $stmt_convo_update->execute();
            }

            // Redirect back to the chat page
            header("Location: messages.php?receiver_id=" . $other_user_id);
            exit();
        } else {
            echo "Error: " . htmlspecialchars($stmt_insert->error); // Escape output
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo $other_username; ?></title>
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
    <div id="section1" class="section">
    <h1>Chat with <?php echo $other_username; ?></h1>

    <div class="chat-box">
        <?php while ($row = $result->fetch_assoc()): ?>
            <p><strong><?php echo htmlspecialchars($row['sender']); ?>:</strong> 
               <?php echo htmlspecialchars($row['message']); ?>
               <span>(<?php echo htmlspecialchars($row['sent_at']); ?>)</span></p>
        <?php endwhile; ?>
    </div>

    <form method="POST" action="">
        <textarea name="message" placeholder="Type your message here..." required></textarea><br>
        <button type="submit">Send</button>
    </form>
    </div>
    <div class="footer">
        <p>Footer Content</p>
    </div>
</body>
</html>
