<?php
include("../function/session.php");
include("../db/dbconn.php");


$admin_id = (int)$_SESSION['id'];
$admin = mysqli_query($conn, "SELECT username FROM admin WHERE adminid = '$admin_id'");
$fetch = mysqli_fetch_assoc($admin);
$admin_username = $fetch['username'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>AlphaWare</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <script src="../js/jquery-1.9.1.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</head>
<body>
<div id="header" style="position:fixed;">
    <img src="../img/logo.jpg" alt="AlphaWare Logo">
    <label>alphaware</label>
    <ul>
        <li><a href="../function/admin_logout.php"><i class="icon-off icon-white"></i>logout</a></li>
        <li>Welcome:&nbsp;&nbsp;&nbsp;<i class="icon-user icon-white"></i><?php echo htmlspecialchars($admin_username); ?></li>
    </ul>
</div>

<br>

<div id="leftnav">
    <ul>
        <li><a href="admin_home.php" style="color:#333;">Dashboard</a></li>
        <li><a href="admin_home.php">Products</a>
            <ul>
                <li><a href="admin_feature.php" style="font-size:15px; margin-left:15px;">Features</a></li>
                <li><a href="admin_product.php" style="font-size:15px; margin-left:15px;">Basketball</a></li>
                <li><a href="admin_football.php" style="font-size:15px; margin-left:15px;">Football</a></li>
                <li><a href="admin_running.php" style="font-size:15px; margin-left:15px;">Running</a></li>
            </ul>
        </li>
        <li><a href="transaction.php">Transactions</a></li>
        <li><a href="customer.php">Customers</a></li>
        <li><a href="message.php">Messages</a></li>
        <li><a href="order.php">Orders</a></li>
    </ul>
</div>

<div id="rightcontent" style="position:absolute; top:10%;">
    <div class="alert alert-info"><center><h2>Orders</h2></center></div>
    <br />
    <div style="width:975px;" class="alert alert-info">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>SHOE</th>
                    <th>Transaction No.</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $Q1 = mysqli_query($conn, "SELECT transaction_id FROM transaction WHERE order_stat = 'Confirmed'");
            while ($r1 = mysqli_fetch_assoc($Q1)) {
                $tid = (int)$r1['transaction_id'];

                $Q2 = mysqli_query($conn, "
                    SELECT p.product_name, p.product_price, td.order_qty
                    FROM transaction_detail td
                    LEFT JOIN product p ON p.product_id = td.product_id
                    WHERE td.transaction_id = '$tid'
                ");

                while ($r2 = mysqli_fetch_assoc($Q2)) {
                    $brand   = htmlspecialchars($r2['product_name']);
                    $p_price = (float)$r2['product_price'];
                    $qty     = (int)$r2['order_qty'];
                    $amount  = $p_price * $qty;

                    echo "<tr>
                            <td>{$brand}</td>
                            <td>{$tid}</td>
                            <td>".formatMoney($amount, true)."</td>
                          </tr>";
                }
            }

            $Q3 = mysqli_query($conn, "SELECT SUM(amount) AS total_amount FROM transaction WHERE order_stat = 'Confirmed'");
            $r3 = mysqli_fetch_assoc($Q3);
            $amnt = $r3['total_amount'] ?? 0;
            echo "<tr><td></td><td>TOTAL :</td><td><b>Php ".formatMoney($amnt, true)."</b></td></tr>";
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function formatMoney($number, $fractional=false) {
    if ($fractional) {
        $number = sprintf('%.2f', $number);
    }
    while (true) {
        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
        if ($replaced != $number) {
            $number = $replaced;
        } else {
            break;
        }
    }
    return $number;
}
?>
</body>
</html>

