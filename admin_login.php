<!DOCTYPE html>
<html>

<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap">
</head>
<body>
    <header>
        <a class="logo" href="index.php">
            <img src="gg.png" alt="GadgetGlitz Logo">
        </a>

        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Login</a>
                    <div class="dropdown-content">
                        <a href="login.php">User Login</a>
                        <a href="admin_login.php">Admin Login</a>
                    </div>
                </li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
        
    </header>

    <div class="container">
        <h1>Admin Login</h1>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="input-with-icon">
                <input type="text" id="name" name="name" placeholder="Username" required>
            </div>
        
            <div class="input-with-icon">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <input type="submit" value="Login">
        </form>
    
        <?php
        session_start();
        include('db_connection.php');

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST["name"];
            $password = $_POST["password"];

            $stmt = $db->prepare("SELECT * FROM admin WHERE name = :name");
            $stmt->bindParam(":name", $name);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && $password == $admin["pass"]) {
                $_SESSION["admin"] = $admin;
                header("Location: dashboard.php");
                exit();
            } else {
                echo '<script type="text/javascript">
                    window.onload = function () {
                        showErrorPopup("Invalid username or password.");
                    };
                </script>';
            }
        }
        ?>
    </div>

    <footer>
        <?php include 'footer.html'; ?>
    </footer>
</body>

</html>