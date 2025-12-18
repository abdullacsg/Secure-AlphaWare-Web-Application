<?php
/* Confirmation Code */
function createRandomPassword($length = 8) {
    $chars = "0123456789"; // Only digits
    $pass = '';

    for ($i = 0; $i < $length; $i++) {
        // Use cryptographically secure random_int
        $num = random_int(0, strlen($chars) - 1);
        $pass .= $chars[$num];
    }

    return $pass;
}

/* values */
$r_id = createRandomPassword();
?>

