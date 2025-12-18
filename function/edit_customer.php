<?php
include ("../db/dbconn.php");
include ("session.php");

if (isset($_POST['edit'])) {
    // CSRF check (optional but recommended)
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        echo "<script>alert('Invalid request. Please try again.'); window.location.href='../account.php';</script>";
        exit();
    }

    $id        = (int) $_SESSION['id'];
    $firstname = trim($_POST['firstname'] ?? '');
    $mi        = trim($_POST['mi'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $country   = trim($_POST['country'] ?? '');
    $zipcode   = preg_replace('/[^0-9]/', '', $_POST['zipcode'] ?? '');
    $mobile    = preg_replace('/[^0-9]/', '', $_POST['mobile'] ?? '');
    $telephone = preg_replace('/[^0-9]/', '', $_POST['telephone'] ?? '');
    $old_password     = $_POST['password'] ?? '';          
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

   

    // Fetch current account info
    $stmt = $conn->prepare("SELECT email, password FROM customer WHERE customerid = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        echo "<script>alert('Account not found.'); window.location.href='../account.php';</script>";
        exit();
    }

    $current_hash = $row['password'];

    // Verify old password
    if (!password_verify($old_password, $current_hash)) {
        echo "<script>alert('Old password is incorrect.'); window.location.href='../account.php';</script>";
        exit();
    }

    // Default: keep old password hash unless a valid change is requested
    $hashed_password = $current_hash;

    // If user wants to change password, enforce strict rules
    if ($new_password !== '' || $confirm_password !== '') {

        // Must match
        if ($new_password !== $confirm_password) {
            echo "<script>alert('New password and confirm password do not match.'); window.location.href='../account.php';</script>";
            exit();
        }

        // Strong password rules
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#_]).{8,}$/';
        if (preg_match($pattern, $new_password) !==1 ) {
            echo "<script>alert('Password must be at least 8 characters and include uppercase, lowercase, number, and special character.'); window.location.href='../account.php';</script>";
            exit();
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    }

    // Update profile (email is NOT updated)
    $stmt = $conn->prepare("UPDATE customer SET 
                firstname = ?, mi = ?, lastname = ?, address = ?,
                country = ?, zipcode = ?, mobile = ?, telephone = ?, 
                password = ?
            WHERE customerid = ?");

    if ($stmt === false) {
        echo "<script>alert('Server error. Please try again later.'); window.location.href='../account.php';</script>";
        exit();
    }

    $stmt->bind_param(
        "sssssssssi",
        $firstname, $mi, $lastname, $address,
        $country, $zipcode, $mobile, $telephone,
        $hashed_password, $id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Account updated successfully.'); window.location.href='../index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Update failed. Please try again.'); window.location.href='../account.php';</script>";
        exit();
    }

    $stmt->close();
}
?>

