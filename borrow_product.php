<?php
require_once('includes/load.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stock_id = $_POST['stock_id'];
    $borrower_name = $_POST['borrower_name'];
    $due_date = $_POST['due_date']; // Get the due date from the form

    // Check if item is available
    $check = $db->query("SELECT * FROM stock WHERE id = '$stock_id' AND status_id = 1");
    if ($check->num_rows == 0) {
        die("Item is not available for borrowing.");
    }

    // Update product status to 'Borrowed' (status_id = 2)
    $db->query("UPDATE stock SET status_id = 2 WHERE id = '$stock_id'");

    // Insert into borrow log, including the due date
    $db->query("INSERT INTO borrowed_items (borrow_id, borrower_name, stat, due_date) VALUES ('$stock_id', '$borrower_name', 'Borrowed', '$due_date')");

    echo "Item borrowed successfully with due date: $due_date!";
}
?>