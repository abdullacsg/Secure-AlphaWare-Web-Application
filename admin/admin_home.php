<?php
    include("../function/session.php");
    include("../db/dbconn.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>AlphaWare</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <script src="../js/jquery-1.7.2.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>

    <!-- Highcharts (use official scripts, remove chart.js which is not needed) -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        Highcharts.chart('container', {
            chart: {
                type: 'pie',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Products share of Shoe Brands as of year <?php echo date("Y"); ?>'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Share',
                colorByPoint: true,
                data: [
                    <?php 
                    $result = mysqli_query($conn, "SELECT brand, COUNT(*) AS cnt FROM product GROUP BY brand");
                    while($row = mysqli_fetch_assoc($result)){
                        $brnd = addslashes($row['brand']); // escape quotes
                        $cnt  = (int)$row['cnt'];
                        echo "{ name: '".$brnd."', y: ".$cnt." },";
                    }
                    ?>
                ]
            }]
        });
    });
    </script>
</head>
<body>
    <div id="header" style="position:fixed;">
        <img src="../img/logo.jpg">
        <label>alphaware</label>
        <?php
            $id = (int) $_SESSION['id'];
            $query = mysqli_query($conn, "SELECT * FROM admin WHERE adminid = '$id'") or die(mysqli_error($conn));
            $fetch = mysqli_fetch_array($query);
        ?>
        <ul>
            <li><a href="../function/admin_logout.php"><i class="icon-off icon-white"></i>logout</a></li>
            <li>Welcome:&nbsp;&nbsp;&nbsp;<i class="icon-user icon-white"></i><?php echo htmlspecialchars($fetch['username']); ?></li>
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
        <div id="container" style="min-width:310px; height:600px; max-width:1000px; margin:0 auto;"></div>
    </div>
</body>
</html>

