<?php
session_start();
include("../db/dbconn.php");



// Ensure an id is provided
if (!isset($_GET['id'])) {
    header("Location: transaction.php?error=missing_id");
    exit();
}

$t_id = (int)$_GET['id']; // cast to int for safety

// Use prepared statement
$stmt = $conn->prepare("UPDATE transaction SET order_stat = 'Cancelled' WHERE transaction_id = ?");
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

// Redirect back to transaction list
header("Location: transaction.php?status=cancelled");
exit();
?>

