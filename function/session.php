<?php
session_start();

// Check if user session is set
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit(); 
}

// Verify that this ID belongs to an admin
include("../db/dbconn.php");
$stmt = $conn->prepare("SELECT adminid FROM admin WHERE adminid = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // Not an admin
    header("Location: ../admin/admin_index.php");
    exit();
}
?>

