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
    <title>AlphaWare - Running Products</title>
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
        <div class="nav1">
            <ul>
                <li><a href="product.php">Basketball</a></li>
                <li>|</li>
                <li><a href="football.php">Football</a></li>
                <li>|</li>
                <li><a href="running.php" class="active" style="color:#111;">Running</a></li>
            </ul>
        </div>

        <div id="content">
            <br /><br />
            <div id="product">
                <?php 
                $stmt = $conn->prepare("SELECT * FROM product WHERE category = ? ORDER BY product_id DESC");
                $category = 'running';
                // Bind the variable category as an string ("s")
                $stmt->bind_param("s", $category);
                $stmt->execute();
                $result = $stmt->get_result();

                while($fetch = $result->fetch_assoc()) {
                    $pid = (int)$fetch['product_id'];

                    $stmt2 = $conn->prepare("SELECT qty FROM stock WHERE product_id = ?");
                    // Bind the variable id as an integer ("i")
                    $stmt2->bind_param("i", $pid);
                    $stmt2->execute();
                    // Execute query and fetch single row
                    $rows = $stmt2->get_result()->fetch_assoc();
                    $stmt2->close();

                    $qty = (int)$rows['qty'];
                    if ($qty > 5) {
                        echo "<div class='float'>";
                        echo "<center>";
                        echo "<a href='details.php?id=".$pid."'>
                              <img class='img-polaroid' src='photo/".htmlspecialchars($fetch['product_image'])."' height='300' width='300' alt='Product Image'></a>";
                        echo htmlspecialchars($fetch['product_name']);
                        echo "<br />";
                        echo "P ".htmlspecialchars($fetch['product_price']);
                        echo "<br />";
                        echo "<h3 class='text-info' style='position:absolute; margin-top:-90px; text-indent:15px;'> Size: ".htmlspecialchars($fetch['product_size'])."</h3>";
                        echo "</center>";
                        echo "</div>";
                    }
                }
                $stmt->close();
                ?>
            </div>
        </div>
    </div>

    <!-- Shared footer -->
    <?php include("footer.php"); ?>

</body>
</html>

