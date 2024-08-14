<?php
// Start the session
session_start();

// Database connection
$servername = "localhost"; // or your database server
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "user_db"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve filter values
$selectedJob = isset($_GET['jobs']) ? $_GET['jobs'] : 'All';
$selectedType = isset($_GET['Type']) ? $_GET['Type'] : 'All';
$minDate = isset($_GET['minDate']) ? $_GET['minDate'] : '';
$maxDate = isset($_GET['maxDate']) ? $_GET['maxDate'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Build SQL query with filters and search term
$sql = "SELECT workdata.work_id, workdata.work_N, workdata.work_img, users.username 
        FROM workdata 
        JOIN users ON workdata.user_id = users.id 
        WHERE 1=1";

// Apply filters to query
if ($selectedJob != 'All') {
    $sql .= " AND workdata.work_N = '" . $conn->real_escape_string($selectedJob) . "'";
}

if ($selectedType != 'All') {
    $sql .= " AND workdata.type = '" . $conn->real_escape_string($selectedType) . "'";
}

if (!empty($minDate) && !empty($maxDate)) {
    $sql .= " AND workdata.date BETWEEN '" . $conn->real_escape_string($minDate) . "' AND '" . $conn->real_escape_string($maxDate) . "'";
} elseif (!empty($minDate)) {
    $sql .= " AND workdata.date >= '" . $conn->real_escape_string($minDate) . "'";
} elseif (!empty($maxDate)) {
    $sql .= " AND workdata.date <= '" . $conn->real_escape_string($maxDate) . "'";
}

// Add search term to query
if (!empty($searchTerm)) {
    $sql .= " AND users.username LIKE '%" . $conn->real_escape_string($searchTerm) . "%'";
}

// Execute query
$result = $conn->query($sql);

if ($result === false) {
    die("SQL Error: " . $conn->error);
}

// Fetch job options for dropdown
$jobOptionsSql = "SELECT work_N, COUNT(*) as count FROM workdata GROUP BY work_N";
$jobOptionsResult = $conn->query($jobOptionsSql);
$jobsOptions = '';
if ($jobOptionsResult->num_rows > 0) {
    while($row = $jobOptionsResult->fetch_assoc()) {
        $jobName = htmlspecialchars($row['work_N']);
        $jobCount = htmlspecialchars($row['count']);
        $selected = $jobName == $selectedJob ? ' selected' : '';
        $jobsOptions .= '<option value="' . $jobName . '"' . $selected . '>' . $jobName . ' (' . $jobCount . ')</option>';
    }
} else {
    $jobsOptions .= '<option value="No jobs">No jobs available</option>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <script>
        function applyFilters() {
            // Get filter values
            var jobs = document.getElementById('jobs').value;
            var type = document.getElementById('Type').value;
            var minDate = document.getElementById('minDate').value;
            var maxDate = document.getElementById('maxDate').value;
            var search = document.getElementById('search').value;

            // Create query string
            var queryString = '?jobs=' + encodeURIComponent(jobs) +
                               '&Type=' + encodeURIComponent(type) +
                               '&minDate=' + encodeURIComponent(minDate) +
                               '&maxDate=' + encodeURIComponent(maxDate) +
                               '&search=' + encodeURIComponent(search);

            // Redirect to updated URL
            window.location.href = window.location.pathname + queryString;
        }

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
    <link rel="stylesheet" href="SearchStyleS.css">
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
                <a onclick="dropdownsFunc()" class="dropbtn"><?php echo $_SESSION['username']; ?></a>
                <img class="userImg" src="../user_img/<?php echo $_SESSION['user_img']; ?>" alt="User Image">
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
    <section class="search">
        <h1>Easy Find Work!</h1>
        <h3>Just one click</h3>
        <form action="" method="GET" class="searchbox">
            <input type="text" id="search" placeholder="Search by username..." name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">🔍</button>
        </form>
    </section>
    <section class="result">
        <section class="filters">
            <label>Job:
            <select name="jobs" id="jobs" onchange="applyFilters()">
                <option value="All">All</option>
                <?php echo $jobsOptions; ?>
            </select>
            </label>
            <label>Type:
            <select name="Type" id="Type" onchange="applyFilters()">
                <option value="All">All</option>
                <option value="OPJ">Once per job</option>
                <option value="SLR">Salary</option>
            </select>
            </label>
            <label>Min Date: <input type="date" name="minDate" id="minDate" onchange="applyFilters()" value="<?php echo htmlspecialchars($minDate); ?>"></label>
            <label>Max Date: <input type="date" name="maxDate" id="maxDate" onchange="applyFilters()" value="<?php echo htmlspecialchars($maxDate); ?>"></label>
        </section>
        <section class="content">
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    // Assuming 'work_id' is the unique identifier for each job post
                    $workId = htmlspecialchars($row["work_id"]);
                    echo '<div class="job-post">';
                    echo '<a href="job_detail.php?id=' . $workId . '">';
                    echo '<h2>' . htmlspecialchars($row["work_N"]) . '</h2>';
                    echo '<img src="../work_img/' . htmlspecialchars($row["work_img"]) . '" alt="Job Image">';
                    echo '<p>Posted by: ' . htmlspecialchars($row["username"]) . '</p>';
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                echo "<p>No jobs found.</p>";
            }
            ?>
        </section>
    </section>
</div>
<div class="footer">
    <p>Footer Content</p>
</div>
</body>
</html>
