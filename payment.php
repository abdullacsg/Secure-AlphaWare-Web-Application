<?php
    include("function/session.php");
    include("db/dbconn.php");
    
    // If user_id is not set in session, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

    // Generate CSRF token if not set
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $id = (int) $_SESSION['id'];

    // Prepared statement for customer lookup
    $stmt = $conn->prepare("SELECT * FROM customer WHERE customerid = ?");
    // Bind the variable id as an integer ("i")
    $stmt->bind_param("i", $id);
    $stmt->execute();
    // Execute query and fetch single row
    $fetch = $stmt->get_result()->fetch_assoc();
    $stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>AlphaWare</title>
    <link rel="icon" href="img/logo.jpg" />
    <link rel="stylesheet" type="text/css" href="css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/jquery-migrate-3.3.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
    <div id="header">
        <img src="img/logo.jpg" alt="AlphaWare Logo">
        <label>alphaware</label>
        <ul>
            <li><a href="function/logout.php"><i class="icon-off icon-white"></i>logout</a></li>
            <li>Welcome:&nbsp;&nbsp;&nbsp;<a href="#profile" data-toggle="modal"><i class="icon-user icon-white"></i>
                <?php echo htmlspecialchars($fetch['firstname'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;
                <?php echo htmlspecialchars($fetch['lastname'], ENT_QUOTES, 'UTF-8'); ?>
            </a></li>
        </ul>
    </div>

    <div id="profile" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:700px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 id="myModalLabel">My Account</h3>
        </div>
        <div class="modal-body">
            <center>
                <table>
                    <tr><td class="profile">Name:</td><td class="profile"><?php echo htmlspecialchars($fetch['firstname'], ENT_QUOTES, 'UTF-8');?>&nbsp;<?php echo htmlspecialchars($fetch['mi'], ENT_QUOTES, 'UTF-8');?>&nbsp;<?php echo htmlspecialchars($fetch['lastname'], ENT_QUOTES, 'UTF-8');?></td></tr>
                    <tr><td class="profile">Address:</td><td class="profile"><?php echo htmlspecialchars($fetch['address'], ENT_QUOTES, 'UTF-8');?></td></tr>
                    <tr><td class="profile">Country:</td><td class="profile"><?php echo htmlspecialchars($fetch['country'], ENT_QUOTES, 'UTF-8');?></td></tr>
                    <tr><td class="profile">ZIP Code:</td><td class="profile"><?php echo htmlspecialchars($fetch['zipcode'], ENT_QUOTES, 'UTF-8');?></td></tr>
                    <tr><td class="profile">Mobile Number:</td><td class="profile"><?php echo htmlspecialchars($fetch['mobile'], ENT_QUOTES, 'UTF-8');?></td></tr>
                    <tr><td class="profile">Telephone Number:</td><td class="profile"><?php echo htmlspecialchars($fetch['telephone'], ENT_QUOTES, 'UTF-8');?></td></tr>
                    <tr><td class="profile">Email:</td><td class="profile"><?php echo htmlspecialchars($fetch['email'], ENT_QUOTES, 'UTF-8');?></td></tr>
                </table>
            </center>
        </div>
        <div class="modal-footer">
            <a href="account.php?id=<?php echo (int)$fetch['customerid']; ?>"><input type="button" class="btn btn-success" name="edit" value="Edit Account"></a>
            <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </div>

    <br>
    <div id="container">
        <div class="nav">    
            <ul>
                <li><a href="home.php"><i class="icon-home"></i>Home</a></li>
                <li><a href="product1.php"><i class="icon-th-list"></i>Product</a></li>
                <li><a href="aboutus1.php"><i class="icon-bookmark"></i>About Us</a></li>
                <li><a href="contactus1.php"><i class="icon-inbox"></i>Contact Us</a></li>
                <li><a href="privacy1.php"><i class="icon-info-sign"></i>Privacy Policy</a></li>
                <li><a href="faqs1.php"><i class="icon-question-sign"></i>FAQs</a></li>
            </ul>
        </div>
        <br>

        <?php
        // Sanitize POST inputs
        $cusid = htmlspecialchars($_POST['cusid'] ?? '', ENT_QUOTES, 'UTF-8');
        $total = (float)($_POST['total'] ?? 0);
        $portal = $_POST['portal'] ?? '';
        $distination = $_POST['distination'] ?? '';
        $transactioncode = htmlspecialchars($_POST['transactioncode'] ?? '', ENT_QUOTES, 'UTF-8');

        $charge = ($portal === 'Delivery') ? 50 : 0;
        $charge1 = ($distination === 'Outside City') ? 50 : 0;
        $grandtotal = $total + $charge + $charge1;
        ?>

        <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_xclick" />
            <input type="hidden" name="business" value="mamma__1330248786_biz@yahoo.com" />
            <input type="hidden" name="item_name" value="<?php echo $cusid; ?>" />
            <input type="hidden" name="item_number" value="<?php echo $transactioncode; ?>" />
            <input type="hidden" name="amount" value="<?php echo $grandtotal; ?>" />
            <input type="hidden" name="no_shipping" value="1" />
            <input type="hidden" name="no_note" value="1" />
            <input type="hidden" name="currency_code" value="PHP" />
            <input type="hidden" name="lc" value="GB" />
            <input type="hidden" name="bn" value="PP-BuyNowBF" />
            <!-- CSRF token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div style="margin:0 auto; width:50px;">
                <input type="image" src="images/button.jpg" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
                <img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
            </div>

            <!-- Payment confirmed -->
            <input type="hidden" name="return" value="http://mammamarias.elementfx.com/showconfirm.php" />
            <!-- Payment cancelled -->
            <input type="hidden" name="cancel_return" value="http://mammamarias.elementfx.com/cancel.php" />
            <input type="hidden" name="rm" value="2" />
            <input type="hidden" name="notify_url" value="http://mammamarias.elementfx.com/ipn.php" />
            <input type="hidden" name="custom" value="any other custom field you want to pass" />
        </form>
    </div>
</body>
</html>

