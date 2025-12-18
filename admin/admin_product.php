<?php
ob_start();
include("../function/session.php");
include("../db/dbconn.php");

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AlphaWare</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>

    <!--Le Facebox-->
    <link href="../facefiles/facebox.css" media="screen" rel="stylesheet" type="text/css" />
    <script src="../facefiles/jquery-1.9.js" type="text/javascript"></script>
    <script src="../facefiles/jquery-1.2.2.pack.js" type="text/javascript"></script>
    <script src="../facefiles/facebox.js" type="text/javascript"></script>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('a[rel*=facebox]').facebox()
    })
    </script>
</head>
<body>
    <div id="header" style="position:fixed;">
        <img src="../img/logo.jpg" alt="AlphaWare Logo">
        <label>alphaware</label>

        <?php
            $id = (int) $_SESSION['id'];
            $stmtHdr = $conn->prepare("SELECT username FROM admin WHERE adminid = ?");
            $stmtHdr->bind_param("i", $id);
            $stmtHdr->execute();
            $resHdr = $stmtHdr->get_result();
            $fetchHdr = $resHdr->fetch_assoc();
            $stmtHdr->close();
            $adminUsername = $fetchHdr ? $fetchHdr['username'] : 'Admin';
        ?>

        <ul>
            <li><a href="../function/admin_logout.php"><i class="icon-off icon-white"></i>logout</a></li>
            <li>Welcome:&nbsp;&nbsp;&nbsp;<i class="icon-user icon-white"></i><?php echo htmlspecialchars($adminUsername, ENT_QUOTES, 'UTF-8'); ?></li>
        </ul>
    </div>

    <br>

    <a href="#add" role="button" class="btn btn-info" data-toggle="modal" style="position:absolute;margin-left:222px; margin-top:140px; z-index:-1000;"><i class="icon-plus-sign icon-white"></i>Add Product</a>
    <div id="add" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:400px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 id="myModalLabel">Add Product...</h3>
        </div>
        <div class="modal-body">
            <form method="post" enctype="multipart/form-data">
            <center>
                <table>
                    <tr>
                        <td><input type="file" name="product_image" required></td>
                    </tr>
                    <?php include("random_id.php"); 
                    echo '<tr>
                        <td><input type="hidden" name="product_code" value="'.htmlspecialchars($code, ENT_QUOTES, 'UTF-8').'" required></td>
                    <tr/>';
                    ?>
                    <tr>
                        <td><input type="text" name="product_name" placeholder="Product Name" style="width:250px;" required></td>
                    <tr/>
                    <tr>
                        <td><input type="text" name="product_price" placeholder="Price" style="width:250px;" required></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="product_size" placeholder="Size" style="width:250px;" maxLength="2" required></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="brand" placeholder="Brand Name" style="width:250px;" required></td>
                    </tr>
                    <tr>
                        <td><input type="number" name="qty" placeholder="No. of Stock" style="width:250px;" required></td>
                    </tr>
                    <tr>
                        <td><input type="hidden" name="category" value="basketball"></td>
                    </tr>
                    <tr>
                        <td><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"></td>
                    </tr>
                </table>
            </center>
        </div>
        <div class="modal-footer">
            <input class="btn btn-primary" type="submit" name="add" value="Add">
            <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
            </form>
        </div>
    </div>

    <?php
    // Add product 
    if (isset($_POST['add'])) {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            echo "<script>alert('Invalid request.'); window.location.href='admin_product.php';</script>";
            exit();
        }

        $product_code  = $_POST['product_code'] ?? '';
        $product_name  = $_POST['product_name'] ?? '';
        $product_price = $_POST['product_price'] ?? '';
        $product_size  = $_POST['product_size'] ?? '';
        $brand         = $_POST['brand'] ?? '';
        $category      = $_POST['category'] ?? 'basketball';
        $qty           = (int)($_POST['qty'] ?? 0);

        $code = rand(0,98987787866533499);

        // File upload secure-ish
        if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
            echo "<script>alert('Image upload failed.'); window.location.href='admin_product.php';</script>";
            exit();
        }
        $origName = $_FILES["product_image"]["name"];
        $temp     = $_FILES["product_image"]["tmp_name"];
        $size     = (int)$_FILES["product_image"]["size"];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $safeName = bin2hex(random_bytes(8)) . '.' . $ext;

        if ($size > 30 * 1024 * 1024) { // keep your original 30GB logic but practical 30MB
            echo "<script>alert('File too large.'); window.location.href='admin_product.php';</script>";
            exit();
        }

        $targetPath = __DIR__ . "/../img/" . $safeName;
	if (!move_uploaded_file($temp, $targetPath)) {
  	  echo "<script>alert('Failed to save image.'); window.location.href='admin_feature.php';</script>";
  	  exit();
	}

        // Prepared insert product
        $priceFloat = (float)$product_price;
        $stmtP = $conn->prepare("INSERT INTO product (product_id, product_name, product_price, product_size, product_image, brand, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtP->bind_param("ssdssss", $product_code, $product_name, $priceFloat, $product_size, $safeName, $brand, $category);
        $stmtP->execute();
        $stmtP->close();

        // Prepared insert stock
        $stmtS = $conn->prepare("INSERT INTO stock (product_id, qty) VALUES (?, ?)");
        $stmtS->bind_param("si", $product_code, $qty);
        $stmtS->execute();
        $stmtS->close();

        exit(header("Location: admin_product.php"));
    }
    ?>

    <div id="leftnav">
        <ul>
            <li><a href="admin_home.php" style="color:#333;">Dashboard</a></li>
            <li><a href="admin_home.php">Products</a>
                <ul>
                    <li><a href="admin_feature.php "style="font-size:15px; margin-left:15px;">Features</a></li>
                    <li><a href="admin_product.php "style="font-size:15px; margin-left:15px;">Basketball</a></li>
                    <li><a href="admin_football.php" style="font-size:15px; margin-left:15px;">Football</a></li>
                    <li><a href="admin_running.php"style="font-size:15px; margin-left:15px;">Running</a></li>
                </ul>
            </li>
            <li><a href="transaction.php">Transactions</a></li>
            <li><a href="customer.php">Customers</a></li>
            <li><a href="message.php">Messages</a></li>
            <li><a href="order.php">Orders</a></li>

        </ul>
    </div>

    <div id="rightcontent" style="position:absolute; top:10%;">
        <div class="alert alert-info"><center><h2>Basket Ball</h2></center></div>
        <br />
        <label style="padding:5px; float:right;"><input type="text" name="filter" placeholder="Search Product here..." id="filter"></label>
        <br />

        <div class="alert alert-info">
        <table class="table table-hover" style="background-color:;">
            <thead>
            <tr style="font-size:20px;">
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Product Sizes</th>
                <th>No. of Stock</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
                $stmtList = $conn->prepare("SELECT product_id, product_image, product_name, product_price, product_size FROM product WHERE category = ? ORDER BY product_id DESC");
                $cat = 'basketball';
                $stmtList->bind_param("s", $cat);
                $stmtList->execute();
                $resList = $stmtList->get_result();

                while ($fetch = $resList->fetch_assoc()) {
                    $id = (int)$fetch['product_id'];

                    $stmtQty = $conn->prepare("SELECT qty FROM stock WHERE product_id = ?");
                    $stmtQty->bind_param("i", $id);
                    $stmtQty->execute();
                    $rowQty = $stmtQty->get_result()->fetch_assoc();
                    $stmtQty->close();

                    $qtyVal = $rowQty ? (int)$rowQty['qty'] : 0;
            ?>
            <tr class="del<?php echo $id?>">
                <td><img class="img-polaroid" src="../photo/<?php echo htmlspecialchars($fetch['product_image'], ENT_QUOTES, 'UTF-8')?>" height="70px" width="80px" alt="Product"></td>
                <td><?php echo htmlspecialchars($fetch['product_name'], ENT_QUOTES, 'UTF-8')?></td>
                <td><?php echo htmlspecialchars($fetch['product_price'], ENT_QUOTES, 'UTF-8')?></td>
                <td><?php echo htmlspecialchars($fetch['product_size'], ENT_QUOTES, 'UTF-8')?></td>
                <td><?php echo $qtyVal; ?></td>
                <td>
                <?php
                echo "<a href='stockin.php?id=".$id."' class='btn btn-success' rel='facebox'><i class='icon-plus-sign icon-white'></i> Stock In</a> ";
                echo "<a href='stockout.php?id=".$id."' class='btn btn-danger' rel='facebox'><i class='icon-minus-sign icon-white'></i> Stock Out</a>";
                ?>
                </td>
            </tr>
            <?php
                }
                $stmtList->close();
            ?>
            </tbody>
        </table>
        </div>

        <?php
        // stock in 
        if (isset($_POST['stockin'])) {
            $pid      = (int)($_POST['pid'] ?? 0);
            $new_stck = (int)($_POST['new_stck'] ?? 0);

            $stmtSi = $conn->prepare("SELECT qty FROM stock WHERE product_id = ?");
            $stmtSi->bind_param("i", $pid);
            $stmtSi->execute();
            $rowSi = $stmtSi->get_result()->fetch_assoc();
            $stmtSi->close();

            if ($rowSi) {
                $total = (int)$rowSi['qty'] + $new_stck;
                $stmtUp = $conn->prepare("UPDATE stock SET qty = ? WHERE product_id = ?");
                $stmtUp->bind_param("ii", $total, $pid);
                $stmtUp->execute();
                $stmtUp->close();
            }

            header("Location:admin_product.php");
            exit();
        }

        // stock out 
        if (isset($_POST['stockout'])) {
            $pid      = (int)($_POST['pid'] ?? 0);
            $new_stck = (int)($_POST['new_stck'] ?? 0);

            $stmtSo = $conn->prepare("SELECT qty FROM stock WHERE product_id = ?");
            $stmtSo->bind_param("i", $pid);
            $stmtSo->execute();
            $rowSo = $stmtSo->get_result()->fetch_assoc();
            $stmtSo->close();

            if ($rowSo) {
                $old_stck = (int)$rowSo['qty'];
                $total    = $old_stck - $new_stck;
                $stmtUp2 = $conn->prepare("UPDATE stock SET qty = ? WHERE product_id = ?");
                $stmtUp2->bind_param("ii", $total, $pid);
                $stmtUp2->execute();
                $stmtUp2->close();
            }

            header("Location:admin_product.php");
            exit();
        }
        ?>
    </div>

</body>
</html>
<script type="text/javascript">
    $(document).ready( function() {
        $('.remove').click( function() {
            var id = $(this).attr("id");
            if(confirm("Are you sure you want to delete this product?")){
                $.ajax({
                    type: "POST",
                    url: "../function/remove.php",
                    data: ({id: id}),
                    cache: false,
                    success: function(html){
                        $(".del"+id).fadeOut(2000, function(){ $(this).remove();});
                    }
                });
            } else {
                return false;
            }
        });
    });
</script>


