<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

if (isset($_POST['delete']) && isset($_POST['orderid'])) {
    $orderid = $_POST['orderid'];

    $sql = "DELETE FROM orders WHERE orderid = :orderid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':orderid', $orderid, PDO::PARAM_INT);
    $stmt->execute();
}

$sql = "SELECT o.*, p.pname, p.price FROM orders o
        JOIN products p ON o.pid = p.pid
        WHERE o.uid = :uid"; 
$stmt = $db->prepare($sql);

if ($stmt) {
    $stmt->bindParam(':uid', $_SESSION['user']['uid'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Error: " . $db->errorInfo()[2];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>User Cart</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="header_nav.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="cart1.css?v=<?php echo time(); ?>">
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
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="myorders.php">My Orders</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
            
        </header>

    <main>
        <div class="container">
            <h2>My Cart</h2>
            <?php
            if (count($result) > 0) {
            ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Order Date</th>
                    <th>Action</th>
                </tr>
                <?php
                foreach ($result as $row) {
                    $item_total = $row['quantity'] * $row['price'];
                    if ($row['confirmed'] === 0) {
                        echo "<tr>";
                        echo "<td>{$row['pname']}</td>";
                        echo "<td>{$row['quantity']}</td>";
                        echo "<td>{$row['price']} Tk</td>";
                        echo "<td>{$item_total} Tk</td>";
                        echo "<td>{$row['order_date']}</td>";
                        echo "<td>
                                <form action='' method='post'>
                                <input type='hidden' name='orderid' value='{$row['orderid']}'>
                                <input type='submit' name='delete' value='Delete' onclick='return confirmDelete()'>
                                </form>
                                </td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>
            <form action="checkout.php" method="post">
                <input type="submit" value="Checkout" class="checkout-button" />
            </form>
            <?php
            
            } else {
                echo "<p>Your cart is empty!</p>"; 
            }
            ?>
        </div>
    </main>
	<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this product?");
    }
	</script>
    <footer>
        <?php include 'footer.html'; ?>
    </footer>
</body>
</html>