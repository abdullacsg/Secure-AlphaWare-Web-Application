<?php
session_start();
include("db/dbconn.php");
include("function/login.php");
include("function/customer_signup.php");

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>

<html>

<head>
    <title>AlphaWare - Contact Us</title>
    <link rel="icon" href="img/logo.jpg" />
    <link rel="stylesheet" type="text/css" href="css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">

    <!-- Latest jQuery -->
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/jquery-migrate-3.3.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

    <!-- Shared header -->
    <?php include("header.php"); ?>

    <!-- Login Modal -->
    <div id="login" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:400px;">
        <form method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h3>Login...</h3>

            </div>
            <div class="modal-body">
                <center>
                    <input type="email" name="email" placeholder="Email" style="width:250px;" required>
                    <input type="password" name="password" placeholder="Password" style="width:250px;" required>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <!-- Add your site key -->
                    <div class="g-recaptcha" data-sitekey="sitekey"></div>
                </center>
            </div>
            <div class="modal-footer">
                <input class="btn btn-primary" type="submit" name="login" value="Login">
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

    <!-- Signup Modal -->
    <div id="signup" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:700px;">
        <form method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h3>Sign Up Here...</h3>
            </div>
            <div class="modal-body">
                <center>
                    <input type="text" name="firstname" placeholder="Firstname" required>
                    <input type="text" name="mi" placeholder="Middle Initial" maxlength="1" required>
                    <input type="text" name="lastname" placeholder="Lastname" required>
                    <input type="text" name="address" placeholder="Address" style="width:430px;" required>
                    <input type="text" name="country" placeholder="Province" required>
                    <input type="text" name="zipcode" placeholder="ZIP Code" required maxlength="4">
                    <input type="text" name="mobile" placeholder="Mobile Number" maxlength="11">
                    <input type="text" name="telephone" placeholder="Telephone Number" maxlength="8">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <!-- Add your site key -->
                    <div class="g-recaptcha" data-sitekey="sitekey"></div>
                </center>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" name="signup" value="Sign Up">
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
    
    <!-- Page content -->
    <div id="container">
        <img src="img/contactus.jpg" style="width:1150px; height:250px; border:1px solid #000;" alt="Contact Us Banner">
        <br /><br />

        <div id="content">
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <table style="position:relative; left:25%;">
                    <tr>
                        <td style="font-size:20px;">Email:</td>
                        <td><input type="email" name="email" 
               placeholder="<?php echo htmlspecialchars($_SESSION['customer_email']); ?>" 
               style="width:400px;"></td>
                    </tr>
                    <tr>
                        <td style="font-size:20px;">Message:</td>
                        <td><textarea name="message" style="width:400px; height:300px;" required></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button class="btn btn-info" name="send" style="width:300px;">
                                <i class="icon icon-ok icon-white"></i> Submit
                            </button>
                            &nbsp;
                            <a href="index.php" class="btn btn-danger" style="width:110px;">
                                <i class="icon icon-remove icon-white"></i> Cancel
                            </a>
                        </td>
                    </tr>
                </table> 
            </form> 
        </div>

        <?php
        if (isset($_POST['send'])) {
            // CSRF token validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                exit("Invalid CSRF token");
            }

            $email   = trim($_POST['email']);
            $message = trim($_POST['message']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                exit("Invalid email format");
            }

            // Prepared statement for inserting contact message
            $stmt = $conn->prepare("INSERT INTO contact (email, message) VALUES (?, ?)");
            // Bind the variable email and message as an integer ("ss") 
            $stmt->bind_param("ss", $email, $message);
            $stmt->execute();
            $stmt->close();
        }
        ?>
    </div>

    <!-- Shared footer -->
    <?php include("footer.php"); ?>

</body>
</html>

