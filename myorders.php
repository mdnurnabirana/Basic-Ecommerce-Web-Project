<?php
include 'db_connection.php'; 

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); 
    exit();
}

$sql = "SELECT p.pname, p.price, o.quantity, o.order_date, p.image_url, o.confirmed FROM orders o
        JOIN products p ON o.pid = p.pid
        WHERE o.uid = :uid AND o.confirmed IN (1, 2)";
$stmt = $db->prepare($sql);

$confirmed_orders = [];

if ($stmt) {
    $stmt->bindParam(':uid', $_SESSION['user']['uid'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $row['total_price'] = $row['quantity'] * $row['price'];
        $confirmed_orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>My Orders</title>
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
        <h2>My Orders</h2>
        <?php if (count($confirmed_orders) > 0) { ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Product Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                    <th>Product Image</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($confirmed_orders as $order) { ?>
                    <tr>
                        <td><?php echo $order['pname']; ?></td>
                        <td><?php echo $order['price']; ?></td>
                        <td><?php echo $order['quantity']; ?></td>
                        <td><?php echo $order['total_price']; ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td><img src="uploads/<?php echo $order['image_url']; ?>" alt="Product Image" class="product-image" style="width: 50px; height: 60px;"></td>
                        <td><?php echo ($order['confirmed'] == 1) ? 'Pending' : (($order['confirmed'] == 2) ? 'Approved' : 'Unknown'); ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No orders available.</p>
        <?php } ?>
    </div>
</main>
    <footer>
        <?php include 'footer.html';?>
    </footer>
</body>
</html>