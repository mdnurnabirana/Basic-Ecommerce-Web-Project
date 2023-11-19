<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pname = $_POST["pname"];
    $price = $_POST["price"];
    $total_quantity = $_POST["total_quantity"];
    $category = $_POST["category"]; 

    $upload_dir = "uploads/";
    $allowed_types = array("jpg", "jpeg", "png");
    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_types)) {
        echo "Error: Only JPG, JPEG, PNG files are allowed.";
        exit;
    }

    $image_name = uniqid() . "." . $file_extension;
    $target_file = $upload_dir . $image_name;

    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    $stmt = $db->prepare("INSERT INTO products (pname, price, total_quantity, image_url, category) VALUES (:pname, :price, :total_quantity, :image_url, :category)");
    $stmt->bindParam(":pname", $pname);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":total_quantity", $total_quantity);
    $stmt->bindParam(":image_url", $image_name);
    $stmt->bindParam(":category", $category); 
    $stmt->execute();

    header("Location: add_product.php");
    exit;
}
?>

<!DOCTYPE html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="add_product.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="header_nav.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap">
</head>
<body>
    <header>
        <a class="logo" href="#">
            <img src="gg.png" alt="GadgetGlitz Logo">
        </a>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Manager Product</a>
                    <div class="dropdown-content">
                        <a href="manage_products.php">Product Stock</a>
                        <a href="add_product.php">Add Products</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Pending Orders</a>
                    <div class="dropdown-content">
                        <a href="pending_orders.php">Pending orders</a>
                        <a href="pending_deliveries.php">Pending Deliveries</a>
                    </div>
                </li>
                <li><a href="users.php">Manage User</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        
    </header>
    <div class="container">
        <main>
            <h1>Add New Product</h1>
            <form method="post" action="add_product.php" enctype="multipart/form-data">
                <label for="pname">Product Name:</label>
                <input type="text" id="pname" name="pname" required>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required>

                <label for="total_quantity">Total Quantity:</label>
                <input type="number" id="total_quantity" name="total_quantity" required>

                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="mobile">Mobile</option>
                    <option value="laptop">Laptop</option>
                    <option value="tablet">Tablet</option>
                    <option value="smartwatch">Smart Watch</option>
                </select>

                <label for="image">Image:</label>
                <input type="file" id="image" name="image" required>

                <input type="submit" value="Add Product">
            </form>
        </main>
    </div>

    <footer>
        <?php include 'footer.html'; ?>
    </footer>
</body>
</html>
