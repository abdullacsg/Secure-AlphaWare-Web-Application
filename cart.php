<?php
session_start();
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

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $pid = (int)$_POST['product_id'];

    // Verify product exists
    $stmt = $conn->prepare("SELECT product_id, product_name, product_price, product_size, product_image 
                            FROM product WHERE product_id = ?");
    // Bind the variable Pid as an integer ("i")   
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    // Execute query and fetch single row
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($product) {
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['qty']++;
        } else {
            $_SESSION['cart'][$pid] = [
                'name'  => $product['product_name'],
                'price' => $product['product_price'],
                'size'  => $product['product_size'],
                'image' => $product['product_image'],
                'qty'   => 1
            ];
        }
    }
}

// Handle Remove Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }
    $rid = (int)$_POST['remove_id'];
    unset($_SESSION['cart'][$rid]);
}

// Handle Update Quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }
    $uid = (int)$_POST['update_id'];
    $newQty = max(1, (int)$_POST['new_qty']); // prevent zero/negative
    if (isset($_SESSION['cart'][$uid])) {
        $_SESSION['cart'][$uid]['qty'] = $newQty;
    }
}

// Handle Empty Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empty_cart'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AlphaWare - Cart</title>
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
        <h2>Shopping Cart</h2>
        <form method="post" style="margin-bottom:10px;">
            <input type="hidden" name="empty_cart" value="1">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="btn btn-warning">Empty Cart</button>
        </form>

        <table class="table">
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Size</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
            <?php
            $total = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $id => $item) {
                    $subtotal = $item['price'] * $item['qty'];
                    $total += $subtotal;
                    echo "<tr>";
                    echo "<td><img src='photo/" . htmlspecialchars($item['image']) . "' width='80' height='80' alt='Product'></td>";
                    echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['size']) . "</td>";
                    echo "<td>Php " . htmlspecialchars($item['price']) . "</td>";
                    echo "<td>
                            <form method='post' style='display:inline;'>
                                <input type='number' name='new_qty' value='" . (int)$item['qty'] . "' min='1' style='width:60px;'>
                                <input type='hidden' name='update_id' value='" . (int)$id . "'>
                                <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                <button type='submit' class='btn btn-info btn-sm'>Update</button>
                            </form>
                          </td>";
                    echo "<td>
                            <form method='post' style='display:inline;'>
                                <input type='hidden' name='remove_id' value='" . (int)$id . "'>
                                <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                                <button type='submit' class='btn btn-danger btn-sm'>Remove</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                echo "<tr><td colspan='5'><strong>Total</strong></td><td><strong>Php " . htmlspecialchars($total) . "</strong></td></tr>";
            } else {
                echo "<tr><td colspan='6'>Your cart is empty.</td></tr>";
            }
            ?>
        </table>

        <?php if (!empty($_SESSION['cart'])): ?>
            <a href="summary.php"><button class="btn btn-primary">Proceed to Checkout</button></a>
        <?php endif; ?>
    </div>

    <!-- Shared footer -->
    <?php include("footer.php"); ?>

</body>
</html>

