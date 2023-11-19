<?php
include 'db_connection.php'; 

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); 
    exit();
}

if (isset($_POST['approve'])) {
    $orderId = $_POST['orderId'];

    $sql_fetch_order = "SELECT pid, quantity FROM orders WHERE orderid = :orderId";
    $stmt_fetch_order = $db->prepare($sql_fetch_order);

    if ($stmt_fetch_order) {
        $stmt_fetch_order->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt_fetch_order->execute();
        $order = $stmt_fetch_order->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $sql_update_order = "UPDATE orders SET confirmed = 2 WHERE orderid = :orderId";
            $stmt_update_order = $db->prepare($sql_update_order);

            if ($stmt_update_order) {
                $stmt_update_order->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                $stmt_update_order->execute();

                $sql_update_product = "UPDATE products SET total_quantity = total_quantity - :quantity WHERE pid = :productId";
                $stmt_update_product = $db->prepare($sql_update_product);

                if ($stmt_update_product) {
                    $stmt_update_product->bindParam(':quantity', $order['quantity'], PDO::PARAM_INT);
                    $stmt_update_product->bindParam(':productId', $order['pid'], PDO::PARAM_INT);
                    $stmt_update_product->execute();
                }
            }
        }
    }

    header("Location: pending_orders.php");
    exit();
}

if (isset($_POST['decline'])) {
    $orderId = $_POST['orderId'];

    $sql = "DELETE FROM orders WHERE orderid = :orderId";
    $stmt = $db->prepare($sql);

    if ($stmt) {
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: pending_orders.php");
    exit();
}

if (isset($_POST['search'])) {
    $searchTransactionID = $_POST['searchTransactionID'];
    $sql = "SELECT p.pname, p.price, o.quantity, o.order_date, p.image_url, o.orderid, pa.transaction_id
            FROM orders o
            JOIN products p ON o.pid = p.pid
            JOIN payment pa ON o.orderid = pa.orderid
            WHERE o.confirmed = 1 AND pa.transaction_id = :searchTransactionID";  
    $stmt = $db->prepare($sql);

    $pending_orders = [];

    if ($stmt) {
        $stmt->bindValue(':searchTransactionID', $searchTransactionID, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $row['total_price'] = $row['quantity'] * $row['price'];
            $pending_orders[] = $row;
        }
    }
}else {
$sql = "SELECT p.pname, p.price, o.quantity, o.order_date, p.image_url, o.orderid, pa.transaction_id
        FROM orders o
        JOIN products p ON o.pid = p.pid
        JOIN payment pa ON o.orderid = pa.orderid
        WHERE o.confirmed = 1
        ORDER BY o.uid ASC";  
$stmt = $db->prepare($sql);

$pending_orders = [];

if ($stmt) {
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $row['total_price'] = $row['quantity'] * $row['price'];
        $pending_orders[] = $row;
    }
}
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Pending Orders</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="pending_orders.css?v=<?php echo time(); ?>">
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
    <section>
        <h2>Pending Orders</h2>
        <div class="search-box">
            <form method="post" action="pending_orders.php">
                <label for="searchTransactionID">Search Transaction ID:</label>
                <input type="text" id="searchTransactionID" name="searchTransactionID" placeholder="Enter Transaction ID">
                <button type="submit" name="search" class="search-button">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

        <?php if (count($pending_orders) > 0) { ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Product Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                    <th>Product Image</th>
                    <th>Transaction ID</th> 
                    <th>Action</th> 
                </tr>
                <?php foreach ($pending_orders as $order) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['pname']); ?></td>
                        <td><?php echo number_format($order['price'], 2); ?></td>
                        <td><?php echo $order['quantity']; ?></td>
                        <td><?php echo number_format($order['total_price'], 2); ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td><img src="uploads/<?php echo htmlspecialchars($order['image_url']); ?>" alt="Product Image" class="product-image" style="width: 50px; height: 60px;"></td>
                        <td><?php echo htmlspecialchars($order['transaction_id']); ?></td>
                        <td>
                            <div class="button-container">
                                <form method="post" action="pending_orders.php">
                                    <input type="hidden" name="orderId" value="<?php echo $order['orderid']; ?>">
                                    <button type="submit" name="approve" class="icon-button approve">
                                        <i class="fas fa-check"></i> 
                                    </button>
                                </form>
                                
                                <form method="post" action="pending_orders.php">
                                    <input type="hidden" name="orderId" value="<?php echo $order['orderid']; ?>">
                                    <button type="submit" name="decline" class="icon-button decline">
                                        <i class="fas fa-times"></i> 
                                    </button>
                                </form>
                            </div>
                        </td>
                                                
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No Pending orders available.</p>
        <?php } ?>
    </section>
    <footer>
        <?php include 'footer.html';?>
    </footer>
</body>
</html>