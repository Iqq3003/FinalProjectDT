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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .topnav {
            display: flex;
            justify-content: space-between;
            background-color: #333;
        }

        .topnav a {
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .topnav a:hover {
            background-color: #ddd;
            color: black;
        }

        .topnav a.active {
            background-color: #04AA6D;
            color: white;
        }

        .navigationMenu {
            display: flex;
        }

        .userMenu {
            display: flex;
            align-items: center;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .show {
            display: block;
        }

        .section {
            margin-left: 10%;
            margin-right: 10%;
            padding: 20px;
            height: 400px;
        }

        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            width: 100%;
            bottom: 0;
            overflow: hidden;
        }
    </style>
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
            <a href="#user">LV.999</a>
            <div class="dropdown">
                <?php if (isset($_SESSION['email'])): ?>
                    <a onclick="dropdownsFunc()" class="dropbtn"><?php echo $_SESSION['email']; ?></a>
                    <div id="myDropdown" class="dropdown-content">
                        <a href="#profile">Profile</a>
                        <a href="#about">Message</a>
                        <a href="#contact">Setting</a>
                    </div>
                <?php else: ?>
                    <a href="../FinalProjectDT/User/Login.php">Login</a>
                <?php endif; ?>
            </div>
            <a href="#LogOut">Log out</a>
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