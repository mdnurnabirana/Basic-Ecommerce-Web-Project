<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
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
                <li><a href="signup.php">Signup</a></li>
            </ul>
        </nav>
        
    </header>
    <main>
    <div class="container">
        <h1>Welcome To GadgetGlitz</h1>
        <h1>Please! Login</h1>
        <form method="post" action="login.php">
            <div class="input-with-icon">
                <input type="text" id="email_or_phone" name="email_or_phone" placeholder="Email or Phone" required>
            </div>
            <div class="input-with-icon">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <input type="submit" value="Login">
        </form>
        <p>Forgot your password? <a href="forgot_password.php">Reset Here</a></p>
    </div>
</main>

    <footer>
        <?php include 'footer.html'; ?>
    </footer>

    <script>
        function showErrorPopup(message) {
            alert(message);
        }
    </script>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email_or_phone = $_POST["email_or_phone"];
        $password = $_POST["password"];

        include 'db_connection.php';

        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email_or_phone OR mobile = :email_or_phone");
            $stmt->bindParam(":email_or_phone", $email_or_phone);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user["active"] == 1) {
                    if (password_verify($password, $user["password"])) {
                        session_start();
                        $_SESSION["user"] = $user;

                        echo '<script type="text/javascript">
                            window.onload = function () {
                                alert("Welcome to GadgetGlitz");
                                window.location.href = "index.php";
                            };
                        </script>';
                    } else {
                        echo '<script type="text/javascript">
                            window.onload = function () {
                                showErrorPopup("Invalid email or password.");
                            };
                        </script>';
                    }
                } else {
                    echo '<script type="text/javascript">
                        window.onload = function () {
                            showErrorPopup("Your account is not active. Please contact support.");
                        };
                    </script>';
                }
            } else {
                echo '<script type="text/javascript">
                    window.onload = function () {
                        showErrorPopup("User doesn\'t exist");
                    };
                </script>';
            }
        } catch (PDOException $e) {
            echo '<script type="text/javascript">
                window.onload = function () {
                    showErrorPopup("Connection failed: ' . $e->getMessage() . '");
                };
            </script>';
        }
    }
    ?>
</body>
</html>