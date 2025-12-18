<!--Add Administrator facebox-->
<div>
<?php
include("../db/dbconn.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submission
if (isset($_POST['stockin'])) {
    $pid      = (int)$_POST['pid'];
    $new_stck = (int)$_POST['new_stck'];

    if ($new_stck > 0) {
        // Update stock safely
        $stmt = $conn->prepare("UPDATE stock SET qty = qty + ? WHERE product_id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $new_stck, $pid);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Stock updated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error updating stock.</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Prepare failed: " . htmlspecialchars($conn->error) . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Please enter a positive stock number.</div>";
    }
}
?>
    <div class="login_title"><span>Stock IN</span></div>
    <br>
    <form method="post">
        <table class="login">
            <tr>
                <td>
                    <input type="hidden" name="pid" value="<?php echo $id; ?>" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="number" name="new_stck" placeholder="Enter No. of Stock" min="1" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <button name="stockin" type="submit" class="btn btn-block btn-large btn-info">
                        <i class="icon-ok-sign icon-white"></i> Save Data
                    </button>
                </td>
            </tr>
        </table>
    </form>
</div><!--/end facebox-->

