<?php
session_start();
include('db/dbconn.php');

if (isset($_POST['login'])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>alert('Invalid request'); window.location.href = 'index.php';</script>";
        exit();
    }

    // reCAPTCHA check
    $captcha = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = "secretkey";	//add your secretkey
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha&remoteip=".$_SERVER['REMOTE_ADDR']);
    $responseKeys = json_decode($response, true);
    if (empty($responseKeys['success'])) {
        echo "<script>alert('reCaptcha verification failed'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Inputs
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        echo "<script>alert('Invalid input'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Query user
    $stmt = $conn->prepare("SELECT customerid, password FROM customer WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Verify password
    if ($row && password_verify($password, $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['id'] = (int)$row['customerid'];
        header("Location: index.php"); // redirect back to index
        exit();
    } else {
        echo "<script>alert('invalid email or password'); window.location.href = 'index.php';</script>";
	exit();
    }
}
?>

