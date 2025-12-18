<?php
session_start();
include('db/dbconn.php');

if (isset($_POST['add'])) {
    // Get and validate inputs
    $cust_id  = (int) ($_POST['customerid'] ?? 0);      
    $prod_id  = (int) ($_POST['product_id'] ?? 0);    // Product ID (not name)
    $price    = (float) ($_POST['product_price'] ?? 0);
    $qty      = (int) ($_POST['qty'] ?? 0);
    $amount   = (float) ($_POST['amount'] ?? 0);

    // Prepare safe SQL statement
    $stmt = $conn->prepare("INSERT INTO cart (prod_id, cust_id, price, qty, amount) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error); 
        exit("An error occurred. Please try again later."); 
    }

    // Bind parameters securely
    $stmt->bind_param("iidid", $prod_id, $cust_id, $price, $qty, $amount);

    
    if ($stmt->execute()) {
        header("Location: product1.php");
        exit();
    } else {
        error_log("Insert failed: " . $stmt->error);
        exit("Unable to add to cart.");
    }

    $stmt->close();
}
?>

