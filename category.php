<?php
session_start();
include 'db_connection.php';

if (isset($_GET['category'])) {
    $category = $_GET['category'];
    
    try {
        $stmt = $db->prepare("SELECT * FROM products WHERE category = :category");
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching products: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Category: <?php echo $category; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap">
</head>

<body>
    <header>
        <a class="logo" href="index.php">
            <img src="gg.png" alt="GadgetGlitz Logo">
        </a>
        <form action="search.php" method="POST" class="search-form">
            <input type="text" name="query" placeholder="Search for products..." class="form-control">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Categories</a>
                    <div class="dropdown-content">
                        <a href="category.php?category=Mobile">Mobile</a>
                        <a href="category.php?category=Tablet">Tablet</a>
                        <a href="category.php?category=Laptop">Laptop</a>
                        <a href="category.php?category=Smart Watch">Smart Watch</a>
                        <a href="category.php?category=Router">Router</a>
                    </div>
                </li>

                <?php if (isset($_SESSION["user"])) { ?>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php } else { ?>
                    <li class="dropdown">
                        <a href="#" class="dropbtn">Login</a>
                        <div class="dropdown-content">
                            <a href="login.php">User Login</a>
                            <a href="admin_login.php">Admin Login</a>
                        </div>
                    </li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php } ?>
                
            </ul>
        </nav>
    </header>

    <main>
        <div class="product-container">
            <div class="product-grid">
                        <?php
                        if (isset($products) && count($products) > 0) {
                            foreach ($products as $product) {
                                echo '<div class="product-item ' . strtolower($product['category']) . '">';
                                echo '<div class="product-image-container zoom-on-hover">';
                                echo '<img src="uploads/' . $product["image_url"] . '" alt="' . $product["pname"] . '" class="product-image">';
                                echo '</div>';
                                echo '<div class="product-details">';
                                echo '<h3 class="product-name">' . $product["pname"] . '</h3>';
                                echo '<p class="product-price">' . $product["price"] . ' Tk</p>';
                                echo '<div class="product-quantity">';
                                echo '<label for="quantity-' . $product['pid'] . '">Quantity:</label>';
                                echo '<div class="quantity-input">';
                                echo '<span class="quantity-decrement" onclick="changeQuantity(' . $product['pid'] . ', -1)">-</span>';
                                echo '<input type="text" id="quantity-' . $product['pid'] . '" name="quantity" value="1" required>';
                                echo '<span class="quantity-increment" onclick="changeQuantity(' . $product['pid'] . ', 1)">+</span>';
                                echo '</div>';
                                echo '</div>';
                                echo '<form method="post" action="index.php" class="product-buy">';
                                    if ($product['total_quantity'] > 0) {
                                        echo '<input type="hidden" name="product_id" value="' . $product['pid'] . '">';
                                        echo '<input type="hidden" id="hidden-quantity-' . $product['pid'] . '" name="hidden_quantity" value="1">';
                                        if (isset($_SESSION["user"])) {
                                            echo '<input type="submit" value="Buy" onclick="showPopup(\'Your Product has been added to the cart.\')">';
                                        } else {
                                            echo '<input type="button" value="Buy" onclick="showPopup(\'Please Login to Purchase.\')">';
                                        }
                                    } else {
                                        echo '<div class="out-of-stock-container">';
                                        echo '<button type="button" disabled>Buy</button>';
                                        echo '<p class="out-of-stock">Out of Stock</p>';
                                        echo '</div>';
                                    }
                                    echo '</form>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p class="coming-soon-message">Coming Soon...............</p>';
                        }
                        ?>
                    </div>
                </div>        
    </main>

    <footer>
        <?php include 'footer.html' ; ?>
    </footer>
    
    <script>
        function showPopup(message) {
            alert(message);
            window.location.href = "index.php";
        }
        function changeQuantity(productId, change) {
            var input = document.getElementById('quantity-' + productId);
            var currentQuantity = parseInt(input.value);
            var newQuantity = currentQuantity + change;

            if (newQuantity >= 1) {
                input.value = newQuantity;
            }
        }
    </script>
</body>
</html>            