<!--Add Administrator facebox-->
<div>
<?php
include("../db/dbconn.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submission
if (isset($_POST['stockout'])) {
    $pid      = (int)$_POST['pid'];
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

        if ($updated_qty < 0) {
            echo "<div class='alert alert-warning'>Not enough stock to remove that amount.</div>";
        } else {
            // Update stock safely
            $stmt2 = $conn->prepare("UPDATE stock SET qty = ? WHERE product_id = ?");
            $stmt2->bind_param("ii", $updated_qty, $pid);
            if ($stmt2->execute()) {
                echo "<div class='alert alert-success'>Stock updated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error updating stock.</div>";
            }
            $stmt2->close();
        }
    } else {
        echo "<div class='alert alert-warning'>Please enter a positive stock number.</div>";
    }
}
?>
    <div class="login_title"><span>Stock OUT</span></div>
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
                    <button name="stockout" type="submit" class="btn btn-block btn-large btn-info">
                        <i class="icon-ok-sign icon-white"></i> Save Data
                    </button>
                </td>
            </tr>
        </table>
    </form>
</div><!--/end facebox-->

