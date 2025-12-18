<?php
session_start();
include("db/dbconn.php");

// If user_id is not set in session, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// PayPal sandbox settings
$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
$paypal_id  = 'yhannaki@gmail.com'; // Business email ID

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper function for site URL
function url(){
    $path = explode('/', str_replace('\\','/',__DIR__));
    return sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME'],
        '/'.$path[count($path)-1]
    );
}

// Fetch customer info
$id = (int) $_SESSION['id'];
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
    <title>AlphaWare - Order Summary</title>
    <link rel="icon" href="img/logo.jpg" />
    <link rel="stylesheet" type="text/css" href="css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>

    <!-- Shared header -->
    <?php include("header.php"); ?>

    <div id="container" class="well" style="background-color:#fff; margin-top:20px;">
        <h2>Order Summary</h2>
        <table class="table">
            <tr>
                <th>Quantity</th>
                <th>Product Name</th>
                <th>Size</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <?php
            $total = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $id => $item) {
                    $subtotal = $item['price'] * $item['qty'];
                    $total += $subtotal;
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($item['qty']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['size']) . "</td>";
                    echo "<td>Php " . htmlspecialchars($item['price']) . "</td>";
                    echo "<td>Php " . htmlspecialchars($subtotal) . "</td>";
                    echo "</tr>";
                }
                echo "<tr><td colspan='4'><strong>Total</strong></td><td><strong>Php " . htmlspecialchars($total) . "</strong></td></tr>";
            } else {
                echo "<tr><td colspan='5'>Your cart is empty.</td></tr>";
            }
            ?>
        </table>

        <?php if (!empty($_SESSION['cart'])): ?>
        <!-- PayPal Form -->
        <form action="<?php echo $paypal_url; ?>" method="post">
            <input type="hidden" name="business" value="<?php echo $paypal_id; ?>">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="item_name" value="AlphaWare Order">
            <input type="hidden" name="amount" value="<?php echo htmlspecialchars($total); ?>">
            <input type="hidden" name="currency_code" value="PHP">
            <input type="hidden" name="cancel_return" value="<?php echo url() ?>/function/cancel.php">
            <input type="hidden" name="return" value="<?php echo url() ?>/function/success.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <button type="submit" class="btn btn-primary">Pay with PayPal</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Shared footer -->
    <?php include("footer.php"); ?>

</body>
</html>

