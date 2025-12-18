<?php
session_start();
include("../function/session.php");
include("../db/dbconn.php");


$admin_id = (int)$_SESSION['id'];

// Fetch admin username
$stmt = $conn->prepare("SELECT username FROM admin WHERE adminid = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$fetch = $stmt->get_result()->fetch_assoc();
$stmt->close();

$admin_username = $fetch['username'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>AlphaWare</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <script src="../js/jquery-1.9.1.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../javascripts/filter.js" type="text/javascript"></script>
</head>
<body>
<div id="header" style="position:fixed;">
    <img src="../img/logo.jpg" alt="AlphaWare Logo">
    <label>alphaware</label>
    <ul>
        <li><a href="../function/admin_logout.php"><i class="icon-off icon-white"></i>logout</a></li>
        <li>Welcome:&nbsp;&nbsp;&nbsp;<i class="icon-user icon-white"></i><?php echo htmlspecialchars($admin_username, ENT_QUOTES, 'UTF-8'); ?></li>
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
    <div class="alert alert-info"><center><h2>Customers</h2></center></div>
    <br />
    <label style="padding:5px; float:right;">
        <input type="text" name="filter" placeholder="Search Customers here..." id="filter">
    </label>
    <br />

    <div class="alert alert-info">
        <table class="table table-hover">
            <thead>
                <tr style="font-size:20px;">
                    <th>Name</th>
                    <th>Address</th>
                    <th>Province</th>
                    <th>Zipcode</th>
                    <th>Mobile</th>
                    <th>Telephone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $result = $conn->query("SELECT firstname, mi, lastname, address, country, zipcode, mobile, telephone, email FROM customer ORDER BY lastname ASC");
            while ($fetch = $result->fetch_assoc()) {
                $name = htmlspecialchars($fetch['firstname'].' '.$fetch['mi'].' '.$fetch['lastname'], ENT_QUOTES, 'UTF-8');
                $address = htmlspecialchars($fetch['address'], ENT_QUOTES, 'UTF-8');
                $country = htmlspecialchars($fetch['country'], ENT_QUOTES, 'UTF-8');
                $zipcode = htmlspecialchars($fetch['zipcode'], ENT_QUOTES, 'UTF-8');
                $mobile = htmlspecialchars($fetch['mobile'], ENT_QUOTES, 'UTF-8');
                $telephone = htmlspecialchars($fetch['telephone'], ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars($fetch['email'], ENT_QUOTES, 'UTF-8');
                echo "<tr>
                        <td>{$name}</td>
                        <td>{$address}</td>
                        <td>{$country}</td>
                        <td>{$zipcode}</td>
                        <td>{$mobile}</td>
                        <td>{$telephone}</td>
                        <td>{$email}</td>
                      </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

