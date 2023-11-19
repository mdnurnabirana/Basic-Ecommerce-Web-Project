<?php
include 'db_connection.php'; 

session_start();

$verificationMessage = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST["verification_code"];
    $stored_code = $_SESSION["verification_code"];

    if ($entered_code == $stored_code) {
        $user = $_SESSION["user"];
   
        $hashed_password = password_hash($user["password"], PASSWORD_BCRYPT);
		
        $stmt = $db->prepare("INSERT INTO users (name, email, mobile, address, gender, password, profile_picture_url, active) VALUES (:name, :email, :mobile, :address, :gender, :password, :profile_picture_url, 1)");
        $stmt->bindParam(":name", $user["full_name"]);
        $stmt->bindParam(":email", $user["email"]);
        $stmt->bindParam(":mobile", $user["mobile"]);
        $stmt->bindParam(":address", $user["address"]);
        $stmt->bindParam(":gender", $user["gender"]);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":profile_picture_url", $user["profile_picture_path"]);
        $stmt->execute();
		
        $verificationMessage = "Verification successful. You can now log in.";
    } else {
        $verificationMessage = "Verification code is incorrect. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>User Login</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="verification.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="header_nav.css?v=<?php echo time(); ?>">
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
    <div class="verification-container">
        <h1>Email Verification</h1>
        <p>Please enter the verification code sent to your email:</p>
        <form class="verification-form" method="post" action="verification.php">
            <label for="verification_code">Verification Code:</label>
            <input type="text" id="verification_code" name="verification_code" required>
            <input type="submit" value="Verify">
        </form>
        <script>
            var verificationMessage = "<?php echo $verificationMessage; ?>";
            if (verificationMessage !== "") {
                alert(verificationMessage);
                if (verificationMessage === "Verification successful. You can now log in.") {
                    window.location.href = "login.php";
                }
            }
        </script>
    </div>
</main>
    <footer>
        <?php include 'footer.html'; ?>
    </footer>
</body>
</html>