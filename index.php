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
    <title>AlphaWare - Home</title>
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
    <div id="container">
	
	
	<div id="carousel">
		<div id="myCarousel" class="carousel slide">
			<div class="carousel-inner">
				<div class="active item" style="padding:0; border-bottom:0 solid #111;"><img src="img/banner1.jpg" class="carousel"></div>
				<div class="item" style="padding:0; border-bottom:0 solid #111;"><img src="img/banner2.jpg" class="carousel"></div>
				<div class="item" style="padding:0; border-bottom:0 solid #111;"><img src="img/banner3.jpg" class="carousel"></div>
			</div>
				<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
				<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
		</div>
	</div>
	

	<div id="video">
		<video controls autoplay width="445px" height="300px">
			<source src="video/commercial.mp4" type="video/mp4">
		</video>
	</div>

	
	<div id="content">
		<div id="product" style="position:relative;">
			<center><h2><legend>Feature Items</legend></h2></center>
			<br />
            <?php
            $stmt = $conn->prepare("SELECT * FROM product WHERE category = ? ORDER BY product_id DESC");
            $category = "feature";
            // Bind the variable category as an integer ("s")
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($fetch = $result->fetch_assoc()) {
                $pid = (int)$fetch['product_id'];

                $stmt2 = $conn->prepare("SELECT qty FROM stock WHERE product_id = ?");
                // Bind the variable id as an integer ("i")
                $stmt2->bind_param("i", $pid);
                $stmt2->execute();
                $rows = $stmt2->get_result()->fetch_assoc();

                $qty = (int)$rows['qty'];
                if ($qty > 5) {
                    echo "<div class='float'>";
                    echo "<center>";
                    echo "<a href='details.php?id=" . htmlspecialchars($fetch['product_id'], ENT_QUOTES, 'UTF-8') . "'>
                          <img class='img-polaroid' src='photo/" . htmlspecialchars($fetch['product_image'], ENT_QUOTES, 'UTF-8') . "' height='300' width='300' alt='Product Image'></a>";
                    echo htmlspecialchars($fetch['product_name'], ENT_QUOTES, 'UTF-8');
                    echo "<br />";
                    echo "P " . htmlspecialchars($fetch['product_price'], ENT_QUOTES, 'UTF-8');
                    echo "<br />";
                    echo "<h3 class='text-info' style='position:absolute; margin-top:-90px; text-indent:15px;'> Size: " . htmlspecialchars($fetch['product_size'], ENT_QUOTES, 'UTF-8') . "</h3>";
                    echo "</center>";
                    echo "</div>";
                }
                $stmt2->close();
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

