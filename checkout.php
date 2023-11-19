<?php
session_start();

include 'db_connection.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmation']) && $_POST['confirmation'] === '1') {
        $userid = $_SESSION['user']['uid'];

        $fetchConfirmedOrderIDsQuery = "SELECT orderid FROM orders WHERE uid = :userid AND confirmed = 0";
        $stmtFetchOrderIDs = $db->prepare($fetchConfirmedOrderIDsQuery);
        $stmtFetchOrderIDs->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmtFetchOrderIDs->execute();
        $confirmedOrderIDs = $stmtFetchOrderIDs->fetchAll(PDO::FETCH_COLUMN);

        if ($confirmedOrderIDs) {
            foreach ($confirmedOrderIDs as $orderid) {
                $paymentMethod = $_POST['payment_method'];
                $transactionId = $_POST['transaction_id'];
                $mobile = $_POST['bkashnumber'];

                $insertPaymentQuery = "INSERT INTO payment (mobile, method, transaction_id, date, orderid) VALUES (:mobile, :method, :transaction_id, NOW(), :orderid)";
                $stmtPayment = $db->prepare($insertPaymentQuery);
                $stmtPayment->bindParam(':mobile', $mobile, PDO::PARAM_STR);
                $stmtPayment->bindParam(':method', $paymentMethod, PDO::PARAM_STR);
                $stmtPayment->bindParam(':transaction_id', $transactionId, PDO::PARAM_STR);
                $stmtPayment->bindParam(':orderid', $orderid, PDO::PARAM_INT);
                $stmtPayment->execute();

                if ($stmtPayment->rowCount() > 0) {
                    $deliveryStatus = "Pending";
                    $deliveryAddress = $_POST['address'];
                    $billingMobile = $_SESSION['user']['mobile'];

                    $insertDeliveryQuery = "INSERT INTO pending_deliveries (orderid, delivery_date, status, delivery_address, mobile) VALUES (:orderid, NOW(), :status, :delivery_address, :billing_mobile)";
                    $stmtDelivery = $db->prepare($insertDeliveryQuery);
                    $stmtDelivery->bindParam(':orderid', $orderid, PDO::PARAM_INT);
                    $stmtDelivery->bindParam(':status', $deliveryStatus, PDO::PARAM_STR);
                    $stmtDelivery->bindParam(':delivery_address', $deliveryAddress, PDO::PARAM_STR);
                    $stmtDelivery->bindParam(':billing_mobile', $billingMobile, PDO::PARAM_STR);
                    $stmtDelivery->execute();

                    if ($stmtDelivery->rowCount() > 0) {
                        $updateOrderQuery = "UPDATE orders SET confirmed = 1 WHERE orderid = :orderid";
                        $stmtUpdateOrder = $db->prepare($updateOrderQuery);
                        $stmtUpdateOrder->bindParam(':orderid', $orderid, PDO::PARAM_INT);
                        $stmtUpdateOrder->execute();
                    } else {
                        echo "Error inserting delivery record for order $orderid: " . $stmtDelivery->errorInfo()[2];
                    }
                } else {
                    echo "Error inserting payment record for order $orderid: " . $stmtPayment->errorInfo()[2];
                }
            }
            header('Location: thanks.php');
            exit();
        } else {
            echo "No confirmed orders found for this user.";
        }

        $stmtFetchOrderIDs->closeCursor();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="header_nav.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="checkout.css?v=<?php echo time(); ?>">
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
        <section>
            <h2>Billing Information</h2>
            <form action="" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $_SESSION['user']['name']; ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $_SESSION['user']['email']; ?>" required>

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo $_SESSION['user']['address']; ?>" required>

                <input type="hidden" name="confirmation" value="1">

                <img src="bkash_payment.png" alt="Bkash Payment" width="100%" height="600">
                <h2>Payment Information</h2>
                <label for="payment_method">Payment Method:</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="bkash">Bkash</option>
                    <option value="nagad">Nagad</option>
                </select>

                <label for="bkashnumber">Bkash/Nagad Number:</label>
                <input type="text" id="bkashnumber" name="bkashnumber" required>

                <label for="transaction_id">Transaction ID:</label>
                <input type="text" id="transaction_id" name="transaction_id" required>

                <input type="submit" value="Place Order" class="place-button">
            </form>
        </section>
    </main>
    <footer>
        <?php include 'footer.html'; ?>
    </footer>
</body>
</html>