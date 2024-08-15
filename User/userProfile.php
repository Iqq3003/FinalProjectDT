<?php
session_start();
include 'connection.php'; // Include your database connection file

// Fetch user data from the database
$user_id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="userProfilestyle.css">
    <title>User Profile</title>
</head>
<body>
    <h1>User Profile</h1>
    <table>
        <tr>
            <th>User Profile</th>
            <td><img class="userImg" src="../user_img/<?php echo htmlspecialchars($user['user_img']); ?>" alt="User Image" style="height: 100px; width: 100px;"></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
        </tr>
        <tr>
            <th>Surname</th>
            <td><?php echo htmlspecialchars($user['surname']); ?></td>
        </tr>
        <tr>
            <th>Age</th>
            <td><?php echo htmlspecialchars($user['age']); ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?php echo htmlspecialchars($user['address']); ?></td>
        </tr>
        <tr>
            <th>Birthday</th>
            <td><?php echo htmlspecialchars($user['birthday']); ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?php echo htmlspecialchars($user['phone']); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
        </tr>
    </table>

    <!-- ปุ่มส่งข้อความ -->
    <form action="messages.php" method="get">
        <input type="hidden" name="receiver_id" value="<?php echo $user_id; ?>">
        <button type="submit">Send Message</button>
    </form>
</body>
</html>
