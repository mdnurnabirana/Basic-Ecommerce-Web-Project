<?php
session_start();
include('db_connection.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user"]["uid"];
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $address = $_POST["address"];
    $gender = $_POST["gender"];

    if (empty($full_name) || empty($email) || empty($mobile)) {
        echo "Error: Full Name, Email, and Mobile are required fields.";
        exit;
    }

    $profile_picture_url = null; 

    if (!empty($_FILES["profile_picture"]["name"])) {
        $profile_picture = $_FILES["profile_picture"];
        $upload_dir = "profile_pictures/";

        $allowed_types = array("jpg", "jpeg", "png");
        $file_extension = strtolower(pathinfo($profile_picture["name"], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_types)) {
            echo "Error: Only JPG, JPEG, PNG files are allowed.";
            exit;
        }

        $profile_picture_url = $upload_dir . uniqid() . "." . $file_extension;
        move_uploaded_file($profile_picture["tmp_name"], $profile_picture_url);
    }

    $stmt = $db->prepare("UPDATE users SET name = :name, email = :email, mobile = :mobile, address = :address, gender = :gender " . (!empty($profile_picture_url) ? ", profile_picture_url = :profile_picture_url" : "") . " WHERE uid = :user_id");
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":name", $full_name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":mobile", $mobile);
    $stmt->bindParam(":address", $address);
    $stmt->bindParam(":gender", $gender);
    if (!empty($profile_picture_url)) {
        $stmt->bindParam(":profile_picture_url", $profile_picture_url);
    }
    $stmt->execute();

    header("Location: profile.php");
    exit;
} else {
    $user_id = $_SESSION["user"]["uid"];
    $stmt = $db->prepare("SELECT * FROM users WHERE uid = :user_id");
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="header_nav.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
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
                <li><a href="change_password.php">Change Password</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="myorders.php">My Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="profile-container">
            <section class="body-section" id="profile-section">
                <h1>Profile Information</h1>
                <?php
                if ($user) {
                    if (!empty($user["profile_picture_url"])) {
                        echo "<img src='{$user["profile_picture_url"]}' alt='Profile Picture' width=250' height='250'>";
                    } else {
                        echo "<p>No profile picture available.</p>";
                    }
                    echo "
                    <h2>{$user["name"]}</h2>
                    <p>Email: {$user["email"]}</p>
                    <p>Mobile: {$user["mobile"]}</p>
                    <p>Address: {$user["address"]}</p>
                    <p>Gender: {$user["gender"]}</p>";
                } 
                ?>
            </section>
            <section class="update-section" id="update-form-section">
                <h1>Update Profile</h1>
                <form method="post" action="profile.php" enctype="multipart/form-data">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo isset($user['name']) ? $user['name'] : ''; ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>" required>

                    <label for="mobile">Mobile:</label>
                    <input type="text" id="mobile" name="mobile" value="<?php echo isset($user['mobile']) ? $user['mobile'] : ''; ?>" required>

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?php echo isset($user['address']) ? $user['address'] : ''; ?>">

                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="male" <?php if (isset($user['gender']) && $user['gender'] === 'male') echo 'selected'; ?>>Male</option>
                        <option value="female" <?php if (isset($user['gender']) && $user['gender'] === 'female') echo 'selected'; ?>>Female</option>
                    </select>

                    <label for="profile_picture">Picture:</label>
                    <input type="file" id="profile_picture" name="profile_picture">
                    <br>
                    <input type="submit" value="Update Profile">
                </form>
            </section>
        </div>
    </main>
    <footer>
        <?php include 'footer.html';?>
    </footer>
</body>
</html>