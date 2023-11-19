<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

include 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["full_name"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $address = $_POST["address"];
    $gender = $_POST["gender"];
    $password = $_POST["password"];
	
	
    $check_mobile_query = $db->prepare("SELECT COUNT(*) FROM users WHERE mobile = :mobile");
    $check_mobile_query->bindParam(":mobile", $mobile);
    $check_mobile_query->execute();
    $count_mobile = $check_mobile_query->fetchColumn();

    $check_email_query = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $check_email_query->bindParam(":email", $email);
    $check_email_query->execute();
    $count_email = $check_email_query->fetchColumn();

    if ($count_mobile > 0) {
        echo '<script type="text/javascript">
            window.onload = function () {
                alert("Mobile number already registered. Please use a different mobile number.");
            };
        </script>';
    } elseif ($count_email > 0) {
        echo '<script type="text/javascript">
            window.onload = function () {
                alert("Email already registered. Please use a different email.");
            };
        </script>';
    } elseif (strlen($mobile) !== 11) {
        echo '<script type="text/javascript">
            window.onload = function () {
                alert("Mobile number should be exactly 11 digits.");
            };
        </script>';
    }elseif (strlen($password) < 6) {
        echo '<script type="text/javascript">
            window.onload = function () {
                alert("Password Must contain atleast 6 character.");
            };
        </script>';
    } else {
        $profilePicturePath = null;
        if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
            $targetDirectory = "profile_pictures/"; 
            $profilePicturePath = $targetDirectory . basename($_FILES["profile_picture"]["name"]);

            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profilePicturePath)) {
            } else {
                echo "Error uploading profile picture.";
            }
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $verification_code = mt_rand(100000, 999999);

        $_SESSION["user"] = [
            "full_name" => $name,
            "email" => $email,
            "mobile" => $mobile,
            "address" => $address,
            "gender" => $gender,
            "password" => $password, 
            "profile_picture_path" => $profilePicturePath,
        ];

        $_SESSION["verification_code"] = $verification_code;

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'your email';
        $mail->Password = 'your pass';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port = 587; 
    
        $mail->setFrom('your email', 'GadgetGlitz');
        $mail->addAddress($email, $name);
       
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        $mail->Body = 'Your verification code: ' . $verification_code;

        $mail->send();

        header("Location: verification.php");
        exit(); 
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="signup.css?v=<?php echo time(); ?>">
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
        <h1>SignUp Page</h1>
        <form method="post" action="signup.php" enctype="multipart/form-data">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="mobile">Mobile:</label>
            <input type="text" id="mobile" name="mobile" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address">

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

            <input type="submit" value="Register">
        </form>
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
</body>

</html>
