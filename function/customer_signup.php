<?php
include('db/dbconn.php');
session_start();

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['signup'])) {
    // CSRF Validation 
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>alert('Invalid request.'); window.location.href = 'index.php';</script>";
        exit();
    }

    // reCAPTCHA Validation 
    $captcha = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = "secretkey";	//add your secretkey
    $response = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey .
        "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
    );
    $responseKeys = json_decode($response, true);
    if (empty($responseKeys['success'])) {
        echo "<script>alert('Captcha verification failed.'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Normalize and validate inputs 
    $firstname  = trim($_POST['firstname'] ?? '');
    $mi         = trim($_POST['mi'] ?? '');
    $lastname   = trim($_POST['lastname'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $country    = trim($_POST['country'] ?? '');
    $zipcode    = preg_replace('/[^0-9]/', '', $_POST['zipcode'] ?? '');
    $mobile     = preg_replace('/[^0-9]/', '', $_POST['mobile'] ?? '');
    $telephone  = preg_replace('/[^0-9]/', '', $_POST['telephone'] ?? '');
    $email      = strtolower(trim($_POST['email'] ?? ''));
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    // Basic validation 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        echo "<script>alert('Invalid input'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Password match check 
    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Strong password rules
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#_]).{8,}$/';
    if (!preg_match($pattern, $password)) {
        echo "<script>alert('Password must be at least 8 characters and include uppercase, lowercase, number, and special character.'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Check if email already exists 
    $stmt_check = $conn->prepare("SELECT customerid FROM customer WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('EMAIL ALREADY EXISTS'); window.location.href = 'index.php';</script>";
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();

    // Hash password securely 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new customer 
    $stmt_insert = $conn->prepare("INSERT INTO customer 
        (firstname, mi, lastname, address, country, zipcode, mobile, telephone, email, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param(
        "ssssssssss",
        $firstname, $mi, $lastname, $address, $country, $zipcode,
        $mobile, $telephone, $email, $hashed_password
    );

    if ($stmt_insert->execute()) {
        
        $_SESSION['id'] = $stmt_insert->insert_id;
        echo "<script>alert('Account created successfully!'); window.location='index.php';</script>";
    } else {
        error_log("Signup insert failed: " . $stmt_insert->error);
        echo "<script>alert('An error occurred. Please try again later.'); window.location.href = 'index.php';</script>";
    }

    $stmt_insert->close();
}
?>

