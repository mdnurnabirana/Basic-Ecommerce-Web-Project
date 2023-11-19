<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'db_connection.php'; 

$email = "";
$verification_error = "";
$show_verification_code = false;
$show_reset_form = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["send_code"])) {
        $email = $_POST["email"];

        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $verification_code = mt_rand(100000, 999999);
            $_SESSION["verification_code"] = $verification_code;
            $_SESSION["email_for_password_reset"] = $email; 

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'your email'; 
            $mail->Password = 'your pass'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587; 

            $mail->setFrom('your email', 'GadgetGlitz');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            $mail->Body = 'Your verification code: ' . $verification_code;

            $mail->send();

            $show_verification_code = true;
        } else {
            $verification_error = "Email does not exist. Please enter a valid email address.";
        }
    } elseif (isset($_POST["verify"])) {
        $entered_code = $_POST["verification_code"];
        $stored_code = $_SESSION["verification_code"];
        $email = $_SESSION["email_for_password_reset"]; 

        if ($entered_code == $stored_code) {
            $show_verification_code = false; 
            $show_reset_form = true;
        } else {
            $verification_error = "Verification code is incorrect. Please try again.";
            $show_verification_code = true; 
        }
    } elseif (isset($_POST["change_password"])) {
        $email = $_SESSION["email_for_password_reset"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];
        
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            
            $sql = "UPDATE users SET password = :password WHERE email = :email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":email", $email);

            $stmt->execute();
            $password_change_success = "Password changed successfully.";
            
            header("Location: login.php");
            exit;
        } else {
            $password_change_error = "Passwords do not match. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Forgot Password</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="header_nav.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="forgot_password.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap">
    </head>
    <body>
        <header>
            <a class="logo" href="index.php">
                <img src="gg.png" alt="GadgetGlitz Logo">
            </a>
    </header>
    <main>
    <div class="container">
        <h1>Forgot Password</h1>
        <?php
        if (isset($verification_error)) {
            echo '<p>' . $verification_error . '</p>';
        }

        if (!$show_verification_code && !$show_reset_form) {
            echo '<form method="post" action="forgot_password.php">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="'.$email.'">
                
                <input type="submit" name="send_code" value="Send Code">
              </form>';
        }

        if ($show_verification_code) {
            echo '<form method="post" action="forgot_password.php">
                    <label for="verification_code">Verification Code:</label>
                    <input type="text" id="verification_code" name="verification_code" required>
                    
                    <input type="submit" name="verify" value="Verify">
                  </form>';
        }

        if ($show_reset_form) {
            echo '<form method="post" action="forgot_password.php">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>

                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    
                    <input type="submit" name="change_password" value="Change Password">
                  </form>';
        }
        ?>
    </div>
</main>
    <footer>
        <?php include 'footer.html'; ?>
    </footer>
</body>
</html>
