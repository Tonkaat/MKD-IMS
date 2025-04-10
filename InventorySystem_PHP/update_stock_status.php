<?php
require_once('includes/load.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stock_id = (int)$_POST['stock_id'];
    $status_id = (int)$_POST['status'];

    // Validate and update status
    if (!empty($stock_id) && !empty($status_id)) {
        $sql = "UPDATE stock SET status_id = '{$status_id}' WHERE id = '{$stock_id}'";
        if ($db->query($sql)) {
            // Success
            header("Location: product.php?status=success&message=Status updated successfully.");
            exit();
        } else {
            // Error
            header("Location: product.php?status=error&message=Failed to update status.");
        }
    } else {
        // Invalid data
        header("Location: product.php?status=error&message=Invalid data.");
    }
}
?>
