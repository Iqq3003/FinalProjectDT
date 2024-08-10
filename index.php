<?php
// Start the session
session_start();
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
    <link rel="stylesheet" href="indexstylesheet.css">
</head>

<body>

    <div class="topnav">
        <div class="navigationMenu">
            <a class="active" href="#home">Home</a>
            <a href="#section1">Job</a>
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
                        <a href="#profile">Profile</a>
                        <a href="#about">Message</a>
                        <a href="#contact">Setting</a>
                        <a href="User/Logout.php">Log out</a>
                    </div>
                <?php else: ?>
                    <a href="../FinalProjectDT/User/Login.php">Login</a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>

    <div id="home" class="section" style="background-color:lightgray;">
        <h2>Home</h2>
        <p>Welcome to the home section.</p>
    </div>

    <div id="section1" class="section" style="background-color:lightblue;">
        <h2>Section 1</h2>
        <p>This is section 1.</p>
    </div>

    <div id="section2" class="section" style="background-color:lightgreen;">
        <h2>Section 2</h2>
        <p>This is section 2.</p>
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