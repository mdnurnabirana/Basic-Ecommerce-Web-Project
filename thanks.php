<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            color: #333;
            font-size: 2.5em;
            margin-top: 20px;
            animation: bounce 1s ease infinite;
        }

        p {
            color: #333;
            font-size: 1.2em;
            margin-top: 20px;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes bounce {
            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        session_start();

        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $customerName = $user['name'];
        } else {
            $customerName = "Valued Customer";
        }

        echo "<h1>Thank You, $customerName!</h1>";
        echo "<p>Your order has been received and will be delivered soon.</p>";
        ?>
    </div>
</body>

</html>