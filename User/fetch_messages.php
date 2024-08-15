// fetch_messages.php
<?php
session_start();
include('connection.php');

if (!isset($_SESSION['email']) || !isset($_GET['receiver_id'])) {
    http_response_code(403);
    echo json_encode([]);
    exit();
}

$receiver_id = $_GET['receiver_id'];
$sender_id = $_SESSION['user_id'];

// ฟังก์ชันสำหรับดึงข้อความระหว่างผู้ใช้สองคน
function getMessages($user1_id, $user2_id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC");
    $stmt->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    $stmt->execute();
    return $stmt->get_result();
}

$messages = getMessages($sender_id, $receiver_id, $conn);

$result = [];
while ($msg = $messages->fetch_assoc()) {
    $result[] = [
        'sender' => $msg['sender_id'] == $_SESSION['user_id'] ? 'You' : htmlspecialchars($_GET['receiver_username']),
        'message' => htmlspecialchars($msg['message']),
        'time' => date('Y-m-d H:i:s', strtotime($msg['sent_at']))
    ];
}

echo json_encode($result);
?>
