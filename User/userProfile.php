<?php
session_start();
include 'connection.php'; // Include your database connection file

// Fetch user data from the database
// Fetch user data from the database
$user_id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$sqlDone = "SELECT work_id, work_N, work_img FROM workdata WHERE worker_id = ?";
$stmtDone = $conn->prepare($sqlDone);

if ($stmtDone === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmtDone->bind_param("i", $user_id);
$stmtDone->execute();
$resultDone = $stmtDone->get_result();
$jobsDone = $resultDone->fetch_all(MYSQLI_ASSOC);

// Fetch jobs created by the user
$sqlCreated = "SELECT work_id, work_N, work_img FROM workdata WHERE user_id = ?";
$stmtCreated = $conn->prepare($sqlCreated);

if ($stmtCreated === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmtCreated->bind_param("i", $user_id);
$stmtCreated->execute();
$resultCreated = $stmtCreated->get_result();
$jobsCreated = $resultCreated->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="userProfilestyle.css">
    <title>User Profile</title>
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
    <style>
    .jobs-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        /* Flex item for each job */
        .job-item {
            flex: 1 1 calc(33% - 40px); /* Adjust the percentage based on how many items per row you want */
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            max-width: 300px;
        }

        .job-item img {
            width: 100%;
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
        }
    </style>
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
                    <img class="userImg" src="../user_img/<?php echo $_SESSION['user_img']; ?>" alt="User Image">
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

    <h2>Jobs Done</h2>
    <div class="jobs-container">
        <?php if (empty($jobsDone)): ?>
            <p>No work</p>
        <?php else: ?>
            <?php foreach ($jobsDone as $job): ?>
                <div class="job-item">
                    <h3><?php echo htmlspecialchars($job['work_N']); ?></h3>
                    <img src="../work_img/<?php echo htmlspecialchars($job['work_img']); ?>" alt="Job Image">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h2>Jobs Created</h2>
    <div class="jobs-container">
        <?php if (empty($jobsCreated)): ?>
            <p>No work</p>
        <?php else: ?>
            <?php foreach ($jobsCreated as $job): ?>
                <div class="job-item">
                    <h3><?php echo htmlspecialchars($job['work_N']); ?></h3>
                    <img src="../work_img/<?php echo htmlspecialchars($job['work_img']); ?>" alt="Job Image">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Message Button -->
    <form action="messages.php" method="get">
        <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($user_id); ?>">
        <button type="submit">Send Message</button>
    </form>
</body>
</html>
