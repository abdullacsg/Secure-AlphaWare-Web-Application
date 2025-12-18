<?php
session_start();

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AlphaWare - Admin Login</title>
    <link rel="icon" href="../img/logo.jpg" />
    <link rel="stylesheet" type="text/css" href="../css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    
    <!-- Latest jQuery -->
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/jquery-migrate-3.3.2.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div id="header">
        <img src="../img/logo.jpg" alt="AlphaWare Logo">
        <label>alphaware</label>
    </div>

    <?php include('../function/admin_login.php'); ?>

    <div id="admin" class="container" style="margin-top:30px;">
        <form method="post" class="well" style="max-width:400px; height:300px; margin:auto;">
            <center>
                <legend>Administrator Login</legend>
                <table class="table table-borderless">
                    <tr>
                        <input type="text" name="username" placeholder="Username" class="form-control" required>
                    </tr>
                    <tr>
                        <input type="password" name="password" placeholder="Password" class="form-control" required>
                    </tr>
                    <tr>
                        
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <!-- Add your site key -->
                  	  <div class="g-recaptcha" data-sitekey="sitekey"></div> 
                        
                    </tr>
                    <tr>
                        
                            <button type="submit" name="enter" style="margin-top:20px;" class="btn btn-primary btn-block">Enter</button>
                        
                    </tr>
                </table>
            </center>
        </form>
    </div>
</body>
</html>

