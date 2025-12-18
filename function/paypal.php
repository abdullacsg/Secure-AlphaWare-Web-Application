<?php
session_start();
include('db/dbconn.php');

if (isset($_POST['pay_now'])) {
    $cid   = (int) $_SESSION['id'];
    $total = (float) ($_POST['total'] ?? 0);

    include("random_code.php");
    $t_id = $r_id;
    $date = date("Y-m-d H:i:s"); // safer format for DB

    // Insert transaction securely
    $stmt = $conn->prepare("INSERT INTO transaction (transaction_id, customerid, amount, order_stat, order_date) 
                            VALUES (?, ?, ?, 'ON HOLD', ?)");
    $stmt->bind_param("sids", $t_id, $cid, $total, $date);
    $stmt->execute();
    $stmt->close();

    $p_id = $_POST['pid'] ?? [];
    $oqty = $_POST['qty'] ?? [];

    // Insert transaction details securely
    $stmt_detail = $conn->prepare("INSERT INTO transaction_detail (product_id, order_qty, transaction_id) 
                                   VALUES (?, ?, ?)");
    foreach ($p_id as $i => $pro_id) {
        $qty = (int) $oqty[$i];
        $stmt_detail->bind_param("iis", $pro_id, $qty, $t_id);
        $stmt_detail->execute();
    }
    $stmt_detail->close();

    //If you want to add PayPal later, insert API calls here

    echo "<script>window.location='summary.php?tid=" . htmlspecialchars($t_id, ENT_QUOTES, 'UTF-8') . "'</script>";
}
?>

