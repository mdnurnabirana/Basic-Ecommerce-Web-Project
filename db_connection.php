<?php
$host = "sql105.infinityfree.com";
$dbname = "if0_34942723_ecommerce";
$username_db = "if0_34942723";
$password_db = "ExgygXOLgzF1";

try {
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $username_db,
        $password_db
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
