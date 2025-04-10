<?php
require_once('includes/load.php');
page_require_level(3);

// report_items.php
if (isset($_POST['stock_id']) && isset($_POST['status'])) {
    $stock_id = (int)$_POST['stock_id'];
    $status = (int)$_POST['status'];

    // Ensure the status is valid
    if ($status == 3 || $status == 4) {
        $query = "UPDATE stock SET status_id = {$status} WHERE id = {$stock_id}";
    } elseif ($status == 1) {
        $query = "UPDATE stock SET status_id = 1 WHERE id = {$stock_id}";
    }

    if ($db->query($query)) {
        // Success
        header("Location: user_inventory.php?status=success&message=Item status updated successfully.");
    } else {
        // Error
        header("Location: user_inventory.php?status=error&message=Failed to update item status.");
    }
}

?>
