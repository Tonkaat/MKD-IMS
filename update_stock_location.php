<?php
require_once('includes/load.php');
page_require_level(3);

if (isset($_POST['stock_id']) && isset($_POST['status'])) {
    $stock_id = (int)$_POST['stock_id'];
    $status = (int)$_POST['status'];

    // Ensure the status is valid (either 3 or 4 for Missing/Lost)
    if ($status == 3 || $status == 4) {
        $query = "UPDATE stock SET status_id = {$status} WHERE id = {$stock_id}";

        if ($db->query($query)) {
            // Success: Redirect with a success message
            header("Location: product.php?status=success&message=Item status updated successfully.");
            exit();
        } else {
            // Error: Redirect with an error message
            header("Location: product.php?status=error&message=Failed to update the item status.");
            exit();
        }
} else {
    // Missing parameters: Redirect with an error message
    header("Location: product.php?status=error&message=Missing parameters.");
    exit();
}
}
?>
