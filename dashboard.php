<?php
session_start();

include 'db_connection.php'; 

if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

$revenueType = isset($_POST['revenue_type']) ? $_POST['revenue_type'] : '';

$sql_revenue = ""; 

$dailyRevenue = 0; 
$monthlyRevenue = 0;

if ($revenueType === "daily") {
    $sql_revenue = "SELECT orders.orderid, products.pname, orders.quantity, products.price, 
                    users.uid, users.name, orders.order_date
                    FROM orders
                    JOIN products ON orders.pid = products.pid
                    JOIN users ON orders.uid = users.uid
                    WHERE DATE(orders.order_date) = CURDATE() AND orders.confirmed = 2";
}elseif ($revenueType === "monthly") {
    $sql_revenue = "SELECT DATE_FORMAT(o.order_date, '%Y-%m') AS 'Month',
                o.orderid, products.pname, o.quantity, products.price, 
                users.uid, users.name, o.order_date
                FROM orders o
                JOIN products ON o.pid = products.pid
                JOIN users ON o.uid = users.uid
                WHERE DATE_FORMAT(o.order_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') 
                AND o.confirmed = 2";
}

$result_revenue = $sql_revenue ? $db->query($sql_revenue) : false;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
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
    <main>
        <div class="container">
            <form method="post">
                <label for="revenue_type">Select Revenue Type:</label>
                <select id="revenue_type" name="revenue_type">
                    <option value="" disabled selected>Choose an option</option>
                    <option value="daily">Daily Sells</option>
                    <option value="monthly">Monthly Sells</option>
                </select>
                <button type="submit">Show Revenue</button>
            </form>
             
            <?php 
            if ($revenueType && $result_revenue !== false && $result_revenue->rowCount() > 0) : ?>
                <h2><?php echo ucfirst($revenueType); ?> Revenue</h2>
                <table>
                    <tr>
                        <?php if ($revenueType === "daily") : ?>
                            <th>Order ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Order Date</th>
                        <?php elseif ($revenueType === "monthly") : ?>
                            <th>Month</th>
                            <th>Order ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Order Date</th>
                        <?php endif; ?>
                    </tr>
                    <?php while ($row_revenue = $result_revenue->fetch(PDO::FETCH_ASSOC)) : ?>
                        <tr>
                            <?php if ($revenueType === "daily") : ?>
                                <td><?php echo $row_revenue['orderid']; ?></td>
                                <td><?php echo $row_revenue['pname']; ?></td>
                                <td><?php echo $row_revenue['quantity']; ?></td>
                                <td><?php echo $row_revenue['price']*$row_revenue['quantity']; ?></td>
                                <td><?php echo $row_revenue['uid']; ?></td>
                                <td><?php echo $row_revenue['name']; ?></td>
                                <td><?php echo $row_revenue['order_date']; ?></td>
                                <?php
                                $dailyRevenue += $row_revenue['quantity'] * $row_revenue['price'];
                                ?>
                            <?php elseif ($revenueType === "monthly") : ?>
                                <td><?php echo $row_revenue['Month']; ?></td>
                                <td><?php echo $row_revenue['orderid']; ?></td>
                                <td><?php echo $row_revenue['pname']; ?></td>
                                <td><?php echo $row_revenue['quantity']; ?></td>
                                <td><?php echo $row_revenue['price']*$row_revenue['quantity']; ?></td>
                                <td><?php echo $row_revenue['uid']; ?></td>
                                <td><?php echo $row_revenue['name']; ?></td>
                                <td><?php echo $row_revenue['order_date']; ?></td>
                                <?php
                                $monthlyRevenue += $row_revenue['quantity'] * $row_revenue['price'];
                                ?>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </table>

                <?php
                if ($revenueType === "daily") {
                    echo '<p>Total Daily Revenue: ' . number_format($dailyRevenue, 2) . ' Tk</p>';
                } elseif ($revenueType === "monthly") {
                    echo '<p>Total Monthly Revenue: ' . number_format($monthlyRevenue, 2) . ' Tk</p>';
                }
                ?>

            <?php else : ?>
                <p>No revenue data available for <?php echo ucfirst($revenueType); ?>.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
<footer>
        <?php include 'footer.html'; ?>
</footer>
</html>
