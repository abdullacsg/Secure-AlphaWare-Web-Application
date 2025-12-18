<?php
session_start();
include("../db/dbconn.php");


// Ensure an id is provided
if (!isset($_GET['id'])) {
    header("Location: transaction.php?error=missing_id");
    exit();
}

$t_id = (int)$_GET['id']; // cast to int for safety

// Confirm transaction
$stmt = $conn->prepare("UPDATE transaction SET order_stat = 'Confirmed' WHERE transaction_id = ?");
if ($stmt === false) {
    error_log("Prepare failed: " . $conn->error);
    header("Location: transaction.php?error=server");
    exit();
}
$stmt->bind_param("i", $t_id);
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    header("Location: transaction.php?error=update_failed");
    $stmt->close();
    exit();
}
$stmt->close();

// Fetch transaction details with products
$stmt = $conn->prepare("
    SELECT td.product_id, td.order_qty, s.qty AS stock_qty
    FROM transaction_detail td
    LEFT JOIN stock s ON s.product_id = td.product_id
    WHERE td.transaction_id = ?
");
$stmt->bind_param("i", $t_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $pid   = (int)$row['product_id'];
    $oqty  = (int)$row['order_qty'];
    $stck  = (int)$row['stock_qty'];

    $stck_out = $stck - $oqty;
    if ($stck_out < 0) {
        $stck_out = 0; // prevent negative stock
    }

    $stmt2 = $conn->prepare("UPDATE stock SET qty = ? WHERE product_id = ?");
    $stmt2->bind_param("ii", $stck_out, $pid);
    $stmt2->execute();
    $stmt2->close();
}
$stmt->close();

// Redirect back to transaction list
header("Location: transaction.php?status=confirmed");
exit();
?>

