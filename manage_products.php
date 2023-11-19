<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['update']) && isset($_POST['pid']) && isset($_POST['update_field']) && isset($_POST['update_value'])) {
    $pid = $_POST['pid'];
    $updateField = $_POST['update_field'];
    $updateValue = $_POST['update_value'];

    $sql = "UPDATE products SET $updateField = :updateValue WHERE pid = :pid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':updateValue', $updateValue, PDO::PARAM_STR);
    $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
    $stmt->execute();   
}

$sql = "SELECT * FROM products";
$result = $db->query($sql);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Manage Product Stock</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="manage_products.css?v=<?php echo time(); ?>">
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
            <h2>Manage Products</h2>
            <table>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Product Image</th>
                    <th>Total Quantity</th>
                </tr>
                <?php
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>{$row['pid']}</td>";
                    echo "<td><div class='attribute-update'><form action='' method='post'>
                        <input type='hidden' name='pid' value='{$row['pid']}'>
                        <input type='hidden' name='update_field' value='pname'>
                        <input type='text' name='update_value' placeholder='New Name' class='update-input' value='{$row['pname']}'>
                        <button type='submit' name='update' class='update-button'><i class='fas fa-check'></i></button>
                    </form></div></td>";
                    echo "<td><div class='attribute-update'><form action='' method='post'>
                        <input type='hidden' name='pid' value='{$row['pid']}'>
                        <input type='hidden' name='update_field' value='price'>
                        <input type='text' name='update_value' placeholder='New Price' class='update-input' value='{$row['price']}'>
                        <button type='submit' name='update' class='update-button'><i class='fas fa-check'></i></button>
                    </form></div></td>";
                    echo "<td><div class='attribute-update'><form action='' method='post'>
                        <input type='hidden' name='pid' value='{$row['pid']}'>
                        <input type='hidden' name='update_field' value='category'>
                        <select name='update_value' class='category-select update-input'>
                            <option value='Mobile' " . ($row['category'] == 'Mobile' ? 'selected' : '') . ">Mobile</option>
                            <option value='Tablet' " . ($row['category'] == 'Tablet' ? 'selected' : '') . ">Tablet</option>
                            <option value='Laptop' " . ($row['category'] == 'Laptop' ? 'selected' : '') . ">Laptop</option>
                            <option value='Smart Watch' " . ($row['category'] == 'Smart Watch' ? 'selected' : '') . ">Smart Watch</option>
                            <option value='Router' " . ($row['category'] == 'Router' ? 'selected' : '') . ">Router</option>
                        </select>
                        <button type='submit' name='update' class='update-button'><i class='fas fa-check'></i></button>
                    </form></div></td>";
                    echo "<td><img src='uploads/{$row['image_url']}' alt='Product Image' width='50' height='50'></td>";
                    echo "<td><div class='attribute-update'><form action='' method='post'>
                        <input type='hidden' name='pid' value='{$row['pid']}'>
                        <input type='hidden' name='update_field' value='total_quantity'>
                        <input type='text' name='update_value' placeholder='New Quantity' class='update-input' value='{$row['total_quantity']}'>
                        <button type='submit' name='update' class='update-button'><i class='fas fa-check'></i></button>
                    </form></div></td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </main>

    <footer>
        <?php include 'footer.html';?>
    </footer>
</body>
</html>