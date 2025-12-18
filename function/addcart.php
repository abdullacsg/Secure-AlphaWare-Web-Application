<?php
    if (isset($_POST['add'])) {
        $prod_id = (int) $_POST['product_id'];
        $cust_id = (int) $_POST['customerid'];

        // Prepare a SQL statement  
        $stmt = $conn->prepare("INSERT INTO cart (prod_id, cust_id) VALUES (?, ?)");
        // Bind the variables as integers ("ii")
        $stmt->bind_param("ii", $prod_id, $cust_id);
        $stmt->execute();
        $stmt->close();

        header("Location: product1.php");
        exit();
    }
?>

