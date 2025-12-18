<?php
session_start();
include('../db/dbconn.php');

if (isset($_POST['id'])) {
    // Cast product ID to integer to prevent injection
    $id = (int) $_POST['id'];

    // Prepare a safe SQL statement
    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error); // Log error internally
        exit("An error occurred. Please try again later."); // Generic message
    }

    // Bind product ID securely
    $stmt->bind_param("i", $id);

    // Execute deletion
    if ($stmt->execute()) {
        echo "Product deleted.";
    } else {
        // Log error instead of showing DB details
        error_log("Delete failed: " . $stmt->error);
        echo "Unable to delete product.";
    }

    $stmt->close();
}
?>

