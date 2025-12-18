<?php
session_start();

// If no admin session, redirect to login
if (!isset($_SESSION['id']) || trim($_SESSION['id']) === '') {
    header("Location: admin_index.php");
    exit();
}

// If session exists, show admin home
include("admin_home.php");
?>

