<?php
session_start();
include('../db/dbconn.php');

if (isset($_POST['enter'])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        echo "<script>alert('Invalid request. Please try again.'); window.location.href='../admin_login.php';</script>";
        exit();
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if ($username === '' || $password === '') {
        echo "<script>alert('Please enter both username and password.'); window.location.href='../admin_login.php';</script>";
        exit();
    }

    // Verify Google reCAPTCHA
    $recaptcha_secret = "secretkey";	//add your secretkey
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $captcha_success = json_decode($verify);

    if (!$captcha_success || !$captcha_success->success) {
        echo "<script>alert('reCAPTCHA verification failed.'); window.location.href='../admin';</script>";
        exit();
    }

    // Prepare a SQL statement
    $stmt = $conn->prepare("SELECT adminid, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && isset($row['password'])) {
        // Verify the entered password against the stored hash
        if (password_verify($password, $row['password'])) {
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);
            $_SESSION['id'] = (int) $row['adminid'];

            header("Location: admin_home.php");
            exit();
        }
    }

    // Generic failure
    echo "<script>alert('Invalid username or password'); window.location.href='../admin';</script>";
    exit();
}
?>

