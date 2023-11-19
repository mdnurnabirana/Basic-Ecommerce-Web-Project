<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="header_nav.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap">
    <style>
    body {
    margin: 0;
    padding: 0;
    font-family: 'Open Sans', Arial, sans-serif;
    line-height: 1.3;
    background-color: #f5f5f5;
}
    
    .form {
        width: 600px;
        margin: 0 auto;
        padding: 20px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 10px;
        background-color: #f7f7f7;
        margin-top: 20px;
        margin-bottom: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form input[type="text"],
    .form input[type="email"],
    .form textarea {
        width: 96%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form input[type="text"]:hover,
    .form input[type="email"]:hover{
        transform: scale(1.05); 
        transition: transform 0.3s ease-in-out; 
    }
    .form textarea {
        width: 96%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        resize: vertical;
    }
    .form textarea:hover{
        transform: scale(1.05); 
        transition: transform 0.3s ease-in-out;
    }

    .form button[type="submit"] {
        background-color: #4ACF50;
    color: white;
    border: none;
    width: 100%;
    margin-top: 15px;
    border-radius: 10px;
    cursor: pointer;
    padding: 12px;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.2s;
    transform-origin: center;
    }

    .form button[type="submit"]:hover{
        background-color: #007bff;
        transform: scale(1.05);
    }

    </style>
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

    <div class="form">
        <p>Contact Us</p>
        <form method="post" action="contact.php">
            <input type="text" placeholder="Name" name="name" autocomplete="off">
            <input type="email" placeholder="Email" name="email" autocomplete="off">
            <input type="text" placeholder="Subject" name="subject" autocomplete="off">
            <textarea name="message" placeholder="Message" cols="30" rows="10"></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
 <?php include 'footer.html'; ?>
</html>

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your email'; 
        $mail->Password = 'your pass'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('sending your email', $name); 
        $mail->addAddress('reciever your second email'); 

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "Name: $name<br>Email: $email<br>Subject: $subject<br>Message: $message";

        $mail->send();
        
            echo '<script type="text/javascript">
                window.onload = function () {
                    showErrorPopup("Message Sent Successfuly");
                };
            </script>';
        } catch (Exception $e) {
            echo '<script type="text/javascript">
                window.onload = function () {
                    showErrorPopup("Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '");
                };
            </script>';
        }
    }
?>
