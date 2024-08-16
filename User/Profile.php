<?php
session_start();
include('connection.php');

// Assuming the user is logged in and their email is stored in the session
if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

$email = $_SESSION['email'];

$sql = "SELECT username, name, surname, age, address, birthday, phone, email, user_img, id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: Login.php");
    exit();
}

$userId = $user['id'];

// Query for jobs done by the user
$sqlDone = "SELECT work_id, work_N, work_img FROM workdata WHERE worker_id = ?";
$stmtDone = $conn->prepare($sqlDone);
$stmtDone->bind_param("i", $userId);
$stmtDone->execute();
$resultDone = $stmtDone->get_result();
$jobsDone = $resultDone->fetch_all(MYSQLI_ASSOC);

// Query for jobs created by the user
$sqlCreated = "SELECT work_id, work_N, work_img FROM workdata WHERE user_id = ?";
$stmtCreated = $conn->prepare($sqlCreated);
$stmtCreated->bind_param("i", $userId);
$stmtCreated->execute();
$resultCreated = $stmtCreated->get_result();
$jobsCreated = $resultCreated->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <div id="Profile" class="section">
    <h1>User Profile</h1>
    <table>
        <tr>
            <th>User Profile</th>
            <td><img  class="userImg" src="../user_img/<?php echo $_SESSION['user_img']; ?>" alt="User Image" style="height: 100px; width: 100px;" ></td>
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
    </div>

    <div>
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
</div>

<div>
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
</div>

    <div class="footer">
        <p>Footer Content</p>
    </div>
    
</body>
</html>
