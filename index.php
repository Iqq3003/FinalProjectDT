<?php
// Start the session
session_start();
include('User/connection.php');

$sql = "SELECT id,username, user_img, user_job FROM users LIMIT 4"; // Adjust the query as needed
$result = $conn->query($sql);

$sql2 = "SELECT workdata.work_id, workdata.work_N, workdata.work_img, users.username 
        FROM workdata 
        JOIN users ON workdata.user_id = users.id 
        WHERE 1=1 ORDER BY work_id DESC LIMIT 4"; // Adjust the query as needed
$result2 = $conn->query($sql2);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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
    <link rel="stylesheet" href="IndexStylesheet.css">
</head>

<body>

    <div class="topnav">
        <div class="navigationMenu">
            <a class="active" href="#">Home</a>
            <a href="Woker/Search.php">Job</a>
            <a href="#section2">Company</a>
            <a href="#section3">About Us</a>
        </div>
        <div class="userMenu">
        
            <div class="dropdown">
                <?php if (isset($_SESSION['email'])): ?>
                    <a href="#user">LV.999</a>
                    <a onclick="dropdownsFunc()" class="dropbtn"><?php echo $_SESSION['username']; ?></a>
                    <img  class="userImg" src="user_img/<?php echo $_SESSION['user_img']; ?>" alt="User Image" >
                    <div id="myDropdown" class="dropdown-content">
                        <a href="User/Profile.php">Profile</a>
                        <a href="User/messageslist.php">Message</a>
                        <a href="User/setting.php">Setting</a>
                        <a href="User/Logout.php">Log out</a>
                    </div>
                <?php else: ?>
                    <a href="../FinalProjectDT/User/Login.php">Login</a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>

    <div id="home" class="section" style="padding: 0px;">
    <img src="web_img/_0a4a1d44-5759-4a6e-a1d9-853d1e72c892.jpg" style="object-fit: cover; width: 100%; height: 100%;"/>
    </div>


    <div id="section1" class="section" style="background-color:lightblue;">
        <h2>ผู้คนที่ใช่ ยังไงก็ชอบ</h2>
        <p>พบปะกับคนใหม่ๆ หลากหลายความสามารถได้เลย</p>
        <section class="content">
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    $id = htmlspecialchars($row["id"]);
                    echo '<div class="job-post">';
                    echo '<a href="User/userProfile.php?id=' . $id . '">';
                    echo '<h2>' . htmlspecialchars($row["username"]) . '</h2>';
                    echo '<img src="user_img/' . htmlspecialchars($row["user_img"]) . '" alt="user Image">';
                    echo '<p>Job: ' . htmlspecialchars($row["user_job"]) . '</p>';
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                echo "<p>No jobs found.</p>";
            }
            ?>
        </section>
    </div>

    <div id="section2" class="section" style="background-color:lightgreen;">
        <h2>งานที่เรารัก และงานก็รักเรา</h2>
        <p>งานดีๆ มาแล้ว! รีบไปสมัครแล้วมาทำกัน!</p>
        <section class="content">
            <?php
            if ($result2->num_rows > 0) {
                // Output data of each row
                while($row = $result2->fetch_assoc()) {
                    // Assuming 'work_id' is the unique identifier for each job post
                    $workId = htmlspecialchars($row["work_id"]);
                    echo '<div class="job-post">';
                    echo '<a href="Woker/job_detail.php?id=' . $workId . '">';
                    echo '<h2>' . htmlspecialchars($row["work_N"]) . '</h2>';
                    echo '<img src="work_img/' . htmlspecialchars($row["work_img"]) . '" alt="Job Image">';
                    echo '<p>Posted by: ' . htmlspecialchars($row["username"]) . '</p>';
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                echo "<p>No jobs found.</p>";
            }
            ?>
        </section>
    </div>

    <div id="section3" class="section" style="background-color:lightcoral;">
        <h2>Section 3</h2>
        <p>This is section 3.</p>
    </div>

    <div id="section4" class="section" style="background-color:lightgoldenrodyellow;">
        <h2>Section 4</h2>
        <p>This is section 4.</p>
    </div>

    <div class="footer">
        <p>Footer Content</p>
    </div>

</body>

</html>