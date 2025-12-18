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
	<link rel = "stylesheet" type = "text/css" href="../css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<script src="../js/jquery-1.9.1.js"></script>
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
		<img src="../img/logo.jpg">
		<label>alphaware</label>
		
			
				
			<ul>
				<li><a href="../function/admin_logout.php"><i class="icon-off icon-white"></i>logout</a></li>
				<li>Welcome:&nbsp;&nbsp;&nbsp;<i class="icon-user icon-white"></i><?php echo htmlspecialchars($admin_username); ?></a></li>
			</ul>
	</div>
	
	<br>

		
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
								<td><input type="hidden" name="product_code" value="'.$code.'" required></td>
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
								<td><input type="text" name="brand" placeholder="Brand Name	" style="width:250px;" required></td>
							</tr>
							<tr>
								<td><input type="number" name="qty" placeholder="No. of Stock" style="width:250px;" required></td>
							</tr>
							<tr>
								<td><input type="hidden" name="category" value="basketball"></td>
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
			<div class="alert alert-info"><center><h2>Transactions	</h2></center></div>
			<br />
				<label  style="padding:5px; float:right;"><input type="text" name="filter" placeholder="Search Transactions here..." id="filter"></label>
			<br />
			
			<div class="alert alert-info">
			<table class="table table-hover" style="background-color:;">
				<thead>
				<tr style="font-size:16px;">
					<th>ID</th>
					<th>DATE</th>
					<th>Customer Name</th>
					<th>Total Amount</th>
					<th>Order Status</th>
					<th>Action</th>
				</tr>
				</thead>
				<tbody>
					<?php
$query = $conn->query("
    SELECT t.transaction_id, t.amount, t.order_stat, t.order_date,
           c.firstname, c.lastname
    FROM transaction t
    LEFT JOIN customer c ON c.customerid = t.customerid
");

while ($fetch = $query->fetch_assoc()) {
    $id     = (int)$fetch['transaction_id'];
    $amnt   = htmlspecialchars($fetch['amount']);
    $o_stat = htmlspecialchars($fetch['order_stat']);
    $o_date = htmlspecialchars($fetch['order_date']);
    $name   = htmlspecialchars($fetch['firstname'].' '.$fetch['lastname']);

    echo "<tr>
            <td>{$id}</td>
            <td>{$o_date}</td>
            <td>{$name}</td>
            <td>{$amnt}</td>
            <td>{$o_stat}</td>
            <td>
                <a href='receipt.php?tid={$id}'>View</a>";
    if ($o_stat === 'Confirmed') {
        // no action
    } elseif ($o_stat === 'Cancelled') {
        // no action
    } else {
        echo " | <a class='btn btn-mini btn-info' href='confirm.php?id={$id}'>Confirm</a>
              | <a class='btn btn-mini btn-danger' href='cancel.php?id={$id}'>Cancel</a>";
    }
    echo "</td></tr>";
}
?>

				</tbody>
			</table>
			</div>
			</div>
			
  <?php
if (isset($_POST['stockin'])) {
    $pid = (int)$_POST['pid'];
    $new_stck = (int)$_POST['new_stck'];

    if ($new_stck > 0) {
        $stmt = $conn->prepare("UPDATE stock SET qty = qty + ? WHERE product_id = ?");
        $stmt->bind_param("ii", $new_stck, $pid);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_product.php");
        exit();
    }
}

if (isset($_POST['stockout'])) {
    $pid = (int)$_POST['pid'];
    $new_stck = (int)$_POST['new_stck'];

    if ($new_stck > 0) {
        // Fetch current stock
        $stmt = $conn->prepare("SELECT qty FROM stock WHERE product_id = ?");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        $current_qty = (int)$row['qty'];
        $updated_qty = $current_qty - $new_stck;

        if ($updated_qty >= 0) {
            $stmt2 = $conn->prepare("UPDATE stock SET qty = ? WHERE product_id = ?");
            $stmt2->bind_param("ii", $updated_qty, $pid);
            $stmt2->execute();
            $stmt2->close();
            header("Location: admin_product.php");
            exit();
        } else {
            echo "<div class='alert alert-warning'>Not enough stock to remove that amount.</div>";
        }
    }
}
?>
				
			
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
			}else{
			return false;}
		});				
	});

</script>
