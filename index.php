<?php
session_start();
include 'db_connection.php';

function placeOrder($user_id, $product_id, $quantity, $db) {
    try {
        $stmt = $db->prepare("INSERT INTO orders (uid, pid, quantity) VALUES (:uid, :pid, :quantity)");
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':pid', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error placing the order: " . $e->getMessage();
    }
}

try {
    $stmt = $db->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["product_id"], $_POST["hidden_quantity"], $_SESSION["user"])) {
            $user_id = $_SESSION["user"]["uid"];
            $product_id = $_POST["product_id"];
            $quantity = $_POST["hidden_quantity"];

            placeOrder($user_id, $product_id, $quantity, $db);
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>GadgetGlitz Home</title>
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
    <section class="product-container">
        
        <div class="product-grid">
            <?php foreach ($products as $product) { ?>
                <div class="product-item <?php echo strtolower($product['category']); ?>">
                    <div class="product-image-container zoom-on-hover">
                        <img src="uploads/<?php echo $product['image_url']; ?>" alt="<?php echo $product['pname']; ?>" class="product-image">
                    </div>
                    <div class="product-details">
                        <h3 class="product-name"><?php echo $product['pname']; ?></h3>
                        <p class="product-price"><?php echo $product['price']; ?> Tk</p>
                        <div class="product-quantity">
                            <label for="quantity-<?php echo $product['pid']; ?>">Quantity:</label>
                            <div class="quantity-input">
                                <span class="quantity-decrement" onclick="changeQuantity(<?php echo $product['pid']; ?>, -1)">-</span>
                                <input type="text" id="quantity-<?php echo $product['pid']; ?>" name="quantity" value="1" required>
                                <span class="quantity-increment" onclick="changeQuantity(<?php echo $product['pid']; ?>, 1)">+</span>
                            </div>
                        </div>
                        <form method="post" action="index.php" class="product-buy">
                            <?php if ($product['total_quantity'] > 0) { ?>
                                <input type="hidden" name="product_id" value="<?php echo $product['pid']; ?>">
                                <input type="hidden" id="hidden-quantity-<?php echo $product['pid']; ?>" name="hidden_quantity" value="1">
                                <?php if (isset($_SESSION["user"])) { ?>
                                    <input type="submit" value="Buy" onclick="showPopup('Your Product has been added to the cart.')">
                                <?php } else { ?>
                                    <input type="button" value="Buy" onclick="showPopup('Please Login to Purchase.')">
                                <?php } ?>
                            <?php } else { ?>
                                <div class="out-of-stock-container">
                                    <button type="button" disabled>Buy</button>
                                    <p class="out-of-stock">Out of Stock</p>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
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
                var hiddenInput = document.getElementById('hidden-quantity-' + productId);
                hiddenInput.value = newQuantity;
            }
        }
    </script>

</body>
</html>       
