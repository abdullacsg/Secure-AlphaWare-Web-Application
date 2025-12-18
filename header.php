<?php
session_start();
include("db/dbconn.php");

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<div id="header">
    <img src="img/logo.jpg" alt="AlphaWare Logo">
    <label>alphaware</label>
    <ul>
        <?php if (isset($_SESSION['id'])): ?>
            <?php
            $id = (int)$_SESSION['id'];
            $stmt = $conn->prepare("SELECT firstname, lastname FROM customer WHERE customerid = ?");
            // Bind the variable id as an integer ("i")
            $stmt->bind_param("i", $id);
            $stmt->execute();
            // Execute query and fetch single row
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            ?>
            <li><a href="function/logout.php"><i></i>Logout</a></li>
            &nbsp;|&nbsp;
                <li><a href="account.php"><i></i>Edit Account</a></li>
                &nbsp;|&nbsp;
                </li><i class="icon-user icon-white"></i>
                <?php echo htmlspecialchars($user['firstname']); ?>
                </li>
            
        <?php else: ?>
            <li><a href="#signup" data-toggle="modal">Sign Up </a></li>
            &nbsp;|&nbsp;
            <li><a href="#login" data-toggle="modal">Login</a></li>
        <?php endif; ?>

        <li><a href="cart.php"><i class="icon-shopping-cart icon-white"></i> Cart</a></li>
    </ul>
</div>

<!-- Navigation -->
<div id="container">
    <div class="nav">    
        <ul>
            <li><a href="index.php"><i class="icon-home"></i> Home</a></li>
            <li><a href="product.php"><i class="icon-th-list"></i> Product</a></li>
            <li><a href="aboutus.php"><i class="icon-bookmark"></i> About Us</a></li>
            <li><a href="contactus.php"><i class="icon-inbox"></i> Contact Us</a></li>
            <li><a href="privacy.php"><i class="icon-info-sign"></i> Privacy Policy</a></li>
            <li><a href="faqs.php"><i class="icon-question-sign"></i> FAQs</a></li>
        </ul>
    </div>
</div>

