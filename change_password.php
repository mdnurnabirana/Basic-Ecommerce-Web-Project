<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

include 'db_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"])) {
    $user = $_SESSION["user"];
    $uid = $user["uid"]; 

    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    
    if (strlen($new_password) < 6) {
        $_SESSION["password_change_error"] = "New password must be at least 6 characters long.";
    } else {
        $stmt = $db->prepare("SELECT password FROM users WHERE uid = :uid");
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $stored_password = $row["password"];
            
            if (password_verify($old_password, $stored_password)) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                    $sql = "UPDATE users SET password = :password WHERE uid = :uid";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(":password", $hashed_password);
                    $stmt->bindParam(":uid", $uid);
                    $stmt->execute();

                    $_SESSION["password_change_success"] = "Password changed successfully.";

                    header("Location: login.php");
                    exit;
                } else {
                    $_SESSION["password_change_error"] = "Passwords do not match. Please try again.";
                }
            } else {
                $_SESSION["password_change_error"] = "Old password is incorrect. Please try again.";
            }
        } else {
            $_SESSION["password_change_error"] = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="header_nav.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="change_password.css?v=<?php echo time(); ?>">
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
                <li><a href="profile.php">Profile</a></li>
                <li><a href="change_password.php">Change Password</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="myorders.php">My Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <div class="password-box">
                <h1>Change Password</h1>
                <?php
                if (isset($_SESSION["password_change_success"])) {
                    echo '<p class="success">' . $_SESSION["password_change_success"] . '</p>';
                    unset($_SESSION["password_change_success"]);
                } elseif (isset($_SESSION["password_change_error"])) {
                    echo '<p class="error">' . $_SESSION["password_change_error"] . '</p>';
                    unset($_SESSION["password_change_error"]);
                }
                ?>
                <form method="post" action="change_password.php">
                    <label for="old_password">Old Password:</label>
                    <input type="password" id="old_password" name="old_password" required>

                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>

                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>

                    <input type="submit" name="change_password" value="Change Password">
                </form>
            </div>
        </div>
    </main>
    <footer>
        <?php include 'footer.html'; ?>
    </footer>
</body>
</html>
