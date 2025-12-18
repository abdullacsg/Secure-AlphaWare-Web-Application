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
    <title>AlphaWare</title>
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

    <!-- shared header -->
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

    <!-- Page Content -->
    <div id="container">
        <img src="img/about1.jpg" style="width:1150px; height:250px; border:1px solid #000;" alt="About Us Banner">
        <br /><br />
        <legend>About Us</legend>
        <div id="content">
            <legend><h3>Mission</h3></legend>
            <h4 style="text-indent:60px;">To provide a high quality footwear that suits the athletes’ style and to be one of the leading sports footwear brands in the country.</h4>
            <br />
            <legend><h3>Vision</h3></legend>
            <h4 style="text-indent:60px;">Alphaware, the company that inspires, motivates, and gives determination to sports enthusiasts.</h4>
            <br />
        </div>
    </div>

     <!-- Shared footer -->
    <?php include("footer.php"); ?>

</body>
</html>

