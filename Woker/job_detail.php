<?php
// Start the session
session_start();
include("../User/connection.php");

// Get job ID from the URL
$workId = isset($_GET['id']) ? $_GET['id'] : '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_job'])) {
        // Handle job acceptance
        $workId = $_POST['work_id'];
        $workerId = $_SESSION['user_id'];
        $updateSql = $conn->prepare("UPDATE workdata SET is_get = 1, worker_id = ? WHERE work_id = ?");
        $updateSql->bind_param('ss', $workerId, $workId);
        $updateSql->execute();
    } elseif (isset($_POST['update_is_done'])) {
        // Handle is_done update
        $workId = $_POST['work_id'];
        $is_done = $_POST['is_done'];
        $workerId = $_SESSION['user_id'];
        $updateSql = $conn->prepare("UPDATE workdata SET is_done = ? WHERE work_id = ? AND worker_id = ?");
        $updateSql->bind_param('sss', $is_done, $workId, $workerId);
        $updateSql->execute();
    }
}

// Fetch job details and worker details from the database
$sql = "
    SELECT
        workdata.work_id,
        workdata.worker_id,
        workdata.work_N,
        workdata.work_img,
        workdata.work_description,
        workdata.is_get,
        workdata.is_done,
        posted_by.username AS posted_by_name,
        posted_by.id AS posted_by_id,
        worker.username AS worker_name
    FROM workdata
    JOIN users AS posted_by ON workdata.user_id = posted_by.id
    LEFT JOIN users AS worker ON workdata.worker_id = worker.id
    WHERE workdata.work_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $workId);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("SQL Error: " . $conn->error);
}

// Fetch job details
$jobDetails = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Detail</title>
    <link rel="stylesheet" href="SearchStyles.css">
</head>
<body>
    <div class="topnav">
        <div class="navigationMenu">
            <a href="../index.php">Home</a>
            <a class="active" href="#section1">Job</a>
            <a href="#section2">Company</a>
            <a href="#section3">About Us</a>
        </div>
        <div class="userMenu">
            <div class="dropdown">
                <?php if (isset($_SESSION['email'])): ?>
                    <a href="#user">LV.999</a>
                    <a onclick="dropdownsFunc()" class="dropbtn"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                    <img class="userImg" src="../user_img/<?php echo htmlspecialchars($_SESSION['user_img']); ?>" alt="User Image">
                    <div id="myDropdown" class="dropdown-content">
                        <a href="../User/Profile.php">Profile</a>
                        <a href="#about">Message</a>
                        <a href="#contact">Setting</a>
                        <a href="../User/Logout.php">Log out</a>
                    </div>
                <?php else: ?>
                    <a href="../User/Login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="main">
        <section class="job-detail">
            <?php if ($jobDetails): ?>
                <h1><?php echo htmlspecialchars($jobDetails["work_N"]); ?></h1>
                <img src="../work_img/<?php echo htmlspecialchars($jobDetails["work_img"]); ?>" alt="Job Image">
                <p><strong>Posted by:</strong> <?php echo htmlspecialchars($jobDetails["posted_by_name"]); ?></p>
                <p><strong>Worker:</strong> <?php echo htmlspecialchars($jobDetails["worker_name"]); ?></p>
                <div>
                    <label>Status: 
                        <?php if ($jobDetails["is_done"] == 1): ?>
                            <a>Accept work</a>
                        <?php elseif ($jobDetails["is_done"] == 2): ?>
                            <a>During work</a>
                        <?php elseif ($jobDetails["is_done"] == 3): ?>
                            <a>Finish work</a>
                        <?php endif; ?>
                    </label>
                </div>

                <p><strong>Description:</strong></p>
                <p><?php echo htmlspecialchars($jobDetails["work_description"]); ?></p>

                <?php if ($jobDetails["is_get"] == 0): ?>
                <form method="post" action="">
                    <input type="hidden" name="work_id" value="<?php echo htmlspecialchars($jobDetails["work_id"]); ?>">
                    <input type="submit" name="accept_job" value="Accept">
                </form>
                <form action="../User/messages.php" method="get">
                    <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($jobDetails['posted_by_id']); ?>">
                    <button type="submit">Send Message</button>
                </form>
                <?php else: ?>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $jobDetails["worker_id"]): ?>
                <form method="post" action="">
                    <input type="hidden" name="work_id" value="<?php echo htmlspecialchars($jobDetails["work_id"]); ?>">
                    <label>Status:
                        <select name="is_done">
                            <option value="1" <?php echo $jobDetails["is_done"] == 1 ? 'selected' : ''; ?>>Accept work</option>
                            <option value="2" <?php echo $jobDetails["is_done"] == 2 ? 'selected' : ''; ?>>During work</option>
                            <option value="3" <?php echo $jobDetails["is_done"] == 3 ? 'selected' : ''; ?>>Finish work</option>
                        </select>
                    </label>
                    <input type="submit" name="update_is_done" value="Update">
                </form>
                <?php endif; ?>
                <div class="job_mmenu">
                    <a href="#">Cancel</a>
                    <form action="../User/messages.php" method="get">
                        <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($jobDetails['posted_by_id']); ?>">
                        <button type="submit">Send Message</button>
                    </form>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <p>Job details not found.</p>
            <?php endif; ?>
        </section>
    </div>
    <div class="footer">
        <p>Footer Content</p>
    </div>
</body>
</html>
