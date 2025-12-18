<?php
include('db/dbconn.php');

if (isset($_POST['cash'])) {
    // Normalize inputs
    $customer    = trim($_POST['customer'] ?? '');
    $product     = trim($_POST['product_name'] ?? '');
    $total       = (float) ($_POST['product_price'] ?? 0);
    $destination = trim($_POST['destination'] ?? '');

    // Prepare a SQL statement  
    $stmt = $conn->prepare("INSERT INTO transaction (customer, product, amount, destination, payment) VALUES (?, ?, ?, ?, 'COD')");
    // Bind the variables: customer, product, amount, destination
    $stmt->bind_param("ssds", $customer, $product, $total, $destination);
    $stmt->execute();
    $stmt->close();
}
?>

