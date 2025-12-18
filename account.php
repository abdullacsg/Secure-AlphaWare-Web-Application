<?php
    include("function/session.php");
    include("db/dbconn.php");
    
    // Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AlphaWare</title>
    <link rel="icon" href="img/logo.jpg" />
    <link rel="stylesheet" type="text/css" href="css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    
    <!-- Latest jQuery -->
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/jquery-migrate-3.3.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
    <div id="header">
        <img src="img/logo.jpg">
        <label>alphaware</label>
        
        <?php
            $id = (int) $_SESSION['id'];

            // Prepared statement for customer details in header
            $stmt = $conn->prepare("SELECT * FROM customer WHERE customerid = ?");
            // Bind the variable id as an integer ("i")
            $stmt->bind_param("i", $id);
            $stmt->execute();
            // Execute query and fetch single row
            $fetch = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        ?>
    
        <ul>
            <li><a href="function/logout.php"><i class="icon-off icon-white"></i>logout</a></li>
            <li><a href="#profile" data-toggle="modal">Welcome:&nbsp;&nbsp;&nbsp;<i class="icon-user icon-white"></i>
                <?php echo htmlspecialchars($fetch['firstname'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;
                <?php echo htmlspecialchars($fetch['lastname'], ENT_QUOTES, 'UTF-8'); ?>
            </a></li>
        </ul>    
    </div>

    <div id="container">    
        <?php
            $id = (int) $_SESSION['id'];

            // Prepared statement for customer details in account section
            $stmt = $conn->prepare("SELECT * FROM customer WHERE customerid = ?");
            // Bind the variable id as an integer ("i")
            $stmt->bind_param("i", $id);
            $stmt->execute();
            // Execute query and fetch single row
            $fetch = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Assign values to variables
            $firstname  = $fetch['firstname'];
            $mi         = $fetch['mi'];
            $lastname   = $fetch['lastname'];
            $address    = $fetch['address'];
            $country    = $fetch['country'];
            $zipcode    = $fetch['zipcode'];
            $mobile     = $fetch['mobile'];
            $telephone  = $fetch['telephone'];
            $email      = $fetch['email'];
            $password   = $fetch['password'];
            $customerid = $fetch['customerid'];
        ?>
        <div id="account">
            <form method="POST" action="function/edit_customer.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <center>
                <h3>Edit My Account...</h3>
                    <table>
                        <tr>
                            <td>Firstname:</td><td><input type="text" name="firstname" required value="<?php echo htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8'); ?>"></td>
                        </tr>
                        <tr>
                            <td>M.I. :</td><td><input type="text" name="mi" maxlength="1" required value="<?php echo htmlspecialchars($mi, ENT_QUOTES, 'UTF-8');?>"></td>    
                        </tr>
                        <tr>
                            <td>Lastname:</td><td><input type="text" name="lastname" required value="<?php echo htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8');?>"></td>
                        </tr>
                        <tr>
                            <td>Address:</td><td><input type="text" name="address" required value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8');?>"></td>
                        </tr>
                        <tr>
                            <td>Province:</td><td><input type="text" name="country" required value="<?php echo htmlspecialchars($country, ENT_QUOTES, 'UTF-8');?>"></td>
                        </tr>
                        <tr>
                            <td>ZIP Code:</td><td><input type="text" name="zipcode" required value="<?php echo htmlspecialchars($zipcode, ENT_QUOTES, 'UTF-8');?>" maxlength="4"></td>
                        </tr>    
                        <tr>    
                            <td>Mobile Number:</td><td><input type="text" name="mobile" value="<?php echo htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8');?>" maxlength="11"></td>
                        </tr>
                        <tr>
                            <td>Telephone Number:</td><td><input type="text" name="telephone" value="<?php echo htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8');?>" maxlength="8"></td>
                        </tr>
                        <tr>
                            <td>Email:</td><td><input type="email" name="email" required readonly value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8');?>"></td>
                        </tr>
                        <tr>
                            <td>Old Password:</td><td><input type="password" name="password" required></td>
                        </tr>
                        <tr>
                            <td>New Password:</td><td><input type="password" name="new_password"></td>
                        </tr>
                        <tr>
                            <td>Confirm Password:</td><td><input type="password" name="confirm_password"></td>
                        </tr>
                        <tr>
                            <td></td><td><input type="submit" name="edit" value="Save Changes" class="btn btn-primary">&nbsp;<a href="index.php"><input type="button" name="cancel" value="Cancel" class="btn btn-danger"></a></td>
                        </tr>
                    </table>    
                </center>
            </form>
        </div>
    </div>
</body>
</html>

