<?php
session_start();
include("function/login.php");
include("function/customer_signup.php");
include("db/dbconn.php");

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>

<html>

<head>
    <title>AlphaWare - Privacy Policy</title>
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
    <!-- Page Content -->
    <div id="container">
        <div id="content">
            <legend><h3>Privacy Policy</h3></legend>
            <p>The Alphaware Incorporated respects the privacy of visitors to the alphaware.com website and connected local websites, and takes great care to protect your information. This privacy policy explains what information we collect, how we may use it, and the steps we take to ensure it is protected.</p>
            <hr>
            <h4>Protection of visitors information</h4>
            <p>Your personal information is contained behind secured networks and is only accessible by a limited number of people with special access rights who are required to keep the information confidential. Please keep in mind that whenever you give out personal information online there is a risk that third parties may intercept and use that information. While Alphaware strives to protect its users' personal information and privacy, we cannot guarantee the security of any information you disclose online and you do so at your own risk.</p>
            <hr>
            <h4>Use of cookies</h4>
            <p>A cookie is a small string of information that the website transfers to your computer for identification purposes. Cookies can be used to follow your activity on the website and help us understand your preferences and improve your experience. Cookies are also used to remember your username and password.</p>
            <p>You can turn off all cookies if you prefer not to receive them. You can also have your computer warn you whenever cookies are being used. For both options you must adjust your browser settings. There are also software products available that can manage cookies for you. Please be aware that rejecting cookies can limit the functionality of the website and may prevent access to some features.</p>
            <hr>
            <h4>Online policy</h4>
            <p>This Privacy Policy does not extend to anything inherent in the operation of the internet, and therefore beyond Alphaware's control, and is not to be applied in any manner contrary to applicable law or governmental regulation. This online privacy policy only applies to information collected through our website and not to information collected offline.</p>
        </div>
    </div>

    <!-- Shared footer -->
    <?php include("footer.php"); ?>

</body>
</html>

