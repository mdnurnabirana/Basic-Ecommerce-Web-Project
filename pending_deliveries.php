<?php
include 'db_connection.php'; 

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); 
    exit();
}

if (isset($_POST['markDelivered'])) {
    $deliveryId = $_POST['deliveryId'];

    $sql_update_status = "UPDATE pending_deliveries SET status = 'delivered' WHERE delivery_id = :deliveryId";
    $stmt_update_status = $db->prepare($sql_update_status);

    if ($stmt_update_status) {
        $stmt_update_status->bindParam(':deliveryId', $deliveryId, PDO::PARAM_INT);
        $stmt_update_status->execute();
    }
}

$sql_pending_deliveries = "SELECT * FROM pending_deliveries WHERE status = 'pending'";
$stmt_pending_deliveries = $db->prepare($sql_pending_deliveries);

$pending_deliveries = [];

if ($stmt_pending_deliveries) {
    $stmt_pending_deliveries->execute();
    $pending_deliveries = $stmt_pending_deliveries->fetchAll(PDO::FETCH_ASSOC);
}

$sql_completed_deliveries = "SELECT * FROM pending_deliveries WHERE status = 'delivered'";
$stmt_completed_deliveries = $db->prepare($sql_completed_deliveries);

$completed_deliveries = [];

if ($stmt_completed_deliveries) {
    $stmt_completed_deliveries->execute();
    $completed_deliveries = $stmt_completed_deliveries->fetchAll(PDO::FETCH_ASSOC);
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
        <h2>Pending Deliveries</h2>
        <div class="delivery-box">
            <?php if (count($pending_deliveries) > 0) { ?>
                <table>
                    <tr>
                        <th>Delivery ID</th>
                        <th>Order ID</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($pending_deliveries as $delivery) { ?>
                        <tr>
                            <td><?php echo $delivery['delivery_id']; ?></td>
                            <td><?php echo $delivery['orderid']; ?></td>
                            <td><?php echo $delivery['delivery_date']; ?></td>
                            <td><?php echo $delivery['status']; ?></td>
                            <td>
                                <form method="post" action="pending_deliveries.php">
                                    <input type="hidden" name="deliveryId" value="<?php echo $delivery['delivery_id']; ?>">
                                    <button type="submit" name="markDelivered">Delivered</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p>No pending deliveries available.</p>
            <?php } ?>
        </div>
    </section>
    <section>
        <h2>Completed Deliveries</h2>
        <div class="delivery-box">
            <?php if (count($completed_deliveries) > 0) { ?>
                <table>
                    <tr>
                        <th>Delivery ID</th>
                        <th>Order ID</th>
                        <th>Delivery Date</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($completed_deliveries as $delivery) { ?>
                        <tr>
                            <td><?php echo $delivery['delivery_id']; ?></td>
                            <td><?php echo $delivery['orderid']; ?></td>
                            <td><?php echo $delivery['delivery_date']; ?></td>
                            <td><?php echo $delivery['status']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p>No completed deliveries available.</p>
            <?php } ?>
        </div>
    </section>
    <footer>
        <?php include 'footer.html';?>
    </footer>
</body>
</html>