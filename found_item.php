<?php
require_once('includes/load.php');

// Check if the stock_id and status are set in the URL
if (isset($_GET['stock_id']) && isset($_GET['status'])) {
    $stock_id = (int)$_GET['stock_id'];
    $status = (int)$_GET['status'];

    // Update the item status to available (assuming status '1' means available)
    if ($status == 1) {
        $query = "UPDATE stock SET status_id = 1 WHERE id = {$stock_id}";
        
        if ($db->query($query)) {
            // Redirect back with success message
            header("Location: user_inventory.php?status=success&message=Item has been marked as available again.");
            exit();
        } else {
            // Redirect back with error message
            header("Location: user_inventory.php?status=error&message=Failed to update item status.");
            exit();
        }
    } else {
        // Invalid status
        header("Location: user_inventory.php?status=error&message=Invalid status.");
        exit();
    }
} else {
    // Missing parameters
    header("Location: user_inventory.php?status=error&message=Missing parameters.");
    exit();
}
?>
