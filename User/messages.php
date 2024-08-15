<?php
session_start();
include('connection.php');

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// ฟังก์ชันสำหรับดึงข้อมูลผู้ใช้
function getUserById($id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// ฟังก์ชันสำหรับดึงข้อความระหว่างผู้ใช้สองคน
function getMessages($user1_id, $user2_id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC");
    $stmt->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $stmt->execute();
    return $stmt->get_result();
}

// ฟังก์ชันสำหรับส่งข้อความ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $receiver_id = $_POST['receiver_id'];
    $sender_id = $_SESSION['user_id'];

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
        $stmt->execute();
    }
}

// ตรวจสอบว่ามีการเลือกผู้รับหรือไม่
if (isset($_GET['receiver_id'])) {
    $receiver_id = $_GET['receiver_id'];
    $sender_id = $_SESSION['user_id'];
    $receiver = getUserById($receiver_id, $conn);
    $messages = getMessages($sender_id, $receiver_id, $conn);
} else {
    echo "กรุณาเลือกผู้รับข้อความ.";


    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: auto;
            max-width: 800px;
        }
        .message-list {
            border: 1px solid #ccc;
            padding: 10px;
            height: 400px;
            overflow-y: scroll;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 10px;
        }
        .message .sender {
            font-weight: bold;
        }
        .message .time {
            color: #999;
            font-size: 0.9em;
        }
        .message-form {
            display: flex;
        }
        .message-form textarea {
            flex-grow: 1;
            padding: 10px;
            font-size: 1em;
        }
        .message-form button {
            padding: 10px 20px;
            font-size: 1em;
        }
    </style>
    <script>
        // ฟังก์ชันสำหรับ fetch ข้อความใหม่ ๆ
        function fetchMessages() {
            const receiver_id = '<?php echo $receiver_id; ?>';
            const receiver_username = '<?php echo htmlspecialchars($receiver['username']); ?>';
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `fetch_messages.php?receiver_id=${receiver_id}&receiver_username=${receiver_username}`, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const messages = JSON.parse(xhr.responseText);
                    const messageList = document.querySelector('.message-list');
                    messageList.innerHTML = '';

                    messages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.classList.add('message');
                        messageDiv.innerHTML = `<div class="sender">${msg.sender}:</div>
                                                <div class="content">${msg.message}</div>
                                                <div class="time">${msg.time}</div>`;
                        messageList.appendChild(messageDiv);
                    });

                    // Scroll to bottom after updating messages
                    messageList.scrollTop = messageList.scrollHeight;
                }
            };
            xhr.send();
        }

        // เรียกใช้ฟังก์ชัน fetchMessages ทุก ๆ 5 วินาที
        setInterval(fetchMessages, 5000);
        window.onload = fetchMessages;
    </script>
</head>
<body>

<div class="container">
    <h1>Messenger with <?php echo htmlspecialchars($receiver['username']); ?></h1>

    <div class="message-list">
        <!-- ข้อความจะถูกโหลดโดย JavaScript -->
    </div>

    <form class="message-form" method="post" action="">
        <textarea name="message" rows="3" placeholder="Type your message here..."></textarea>
        <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
        <button type="submit">Send</button>
    </form>
</div>

</body>
</html>

<?php
// ปิดการเชื่อมต่อกับฐานข้อมูล
$conn->close();
?>


<?php
// ปิดการเชื่อมต่อกับฐานข้อมูล
$conn->close();
?>
