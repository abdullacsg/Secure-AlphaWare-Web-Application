<?php
session_start();
include("../db/dbconn.php");


$tid = $_REQUEST['tid'] ?? null;

// PayPal left blank

if (!empty($tid)) {
    // Cast to string safely
    $tid = trim($tid);

    // Prepare safe SQL statement
    $stmt = $conn->prepare("UPDATE transaction SET order_stat = 'Confirmed' WHERE transaction_id = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        exit("An error occurred. Please try again later.");
    }
    // Bind the variable tid as an string ("s")
    $stmt->bind_param("s", $tid);

    if ($stmt->execute()) {
        header("Location: ../home.php?order=confirmed");
        exit();
    } else {
        error_log("Update failed: " . $stmt->error);
        exit("Unable to confirm order.");
    }

    $stmt->close();
} else {
    echo "<h1>Payment Failed</h1>";
}
?>

