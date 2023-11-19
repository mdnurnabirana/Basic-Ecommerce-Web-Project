<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['update']) && isset($_POST['uid'])) {
    $uid = $_POST['uid'];
    $active = $_POST['active'];

    $sql = "UPDATE users SET active = :active WHERE uid = :uid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":active", $active, PDO::PARAM_INT);
    $stmt->bindParam(":uid", $uid, PDO::PARAM_INT);
    $stmt->execute();
    $stmt->closeCursor();
}

if (isset($_POST['delete']) && isset($_POST['uid'])) {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === '1') {
        $uid = $_POST['uid'];
        $sql = "DELETE FROM users WHERE uid = :uid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":uid", $uid, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
}

$sql = "SELECT * FROM users";
$result = $db->query($sql);
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
            <h2>Manage Users</h2>
            <table>
                <tr>
                    <th>UID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Address</th>
                    <th>Action</th>
                    <th>Delete</th>
                </tr>
                <?php
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>{$row['uid']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['mobile']}</td>";
                    echo "<td>{$row['address']}</td>";
                    echo "<td>
                        <form action='' method='post'>
                            <input type='hidden' name='uid' value='{$row['uid']}'>
                            <select name='active'>
                                <option value='1' " . ($row['active'] == 1 ? 'selected' : '') . ">Active</option>
                                <option value='0' " . ($row['active'] == 0 ? 'selected' : '') . ">Inactive</option>
                            </select>
                            <input type='submit' name='update' value='Update'>
                        </form>
                    </td>";
                    echo "<td>
                        <form id='delete-form-{$row['uid']}' action='' method='post'>
                            <input type='hidden' name='uid' value='{$row['uid']}'>
                            <input type='hidden' name='confirm_delete' value='0'> 
                            <button type='button' onclick='confirmDelete({$row['uid']})'>Delete</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }

                $db = null;
                ?>
            </table>
        </div>
    </main>

    <footer>
        <?php include 'footer.html';?>
    </footer>

    <script>
        function confirmDelete(uid) {
            var result = confirm('Are you sure you want to delete this user?');
            if (result) {
                var form = document.getElementById('delete-form-' + uid);
                form.querySelector('input[name="confirm_delete"]').value = '1';
                form.submit();
            }
        }
    </script>
</body>
</html>