<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
require_once('includes/load.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $borrow_id = $data['borrow_id'];  

    // Check if item is borrowed
    $query = "SELECT stock.id AS stock_id FROM borrowed_items 
              JOIN stock ON borrowed_items.borrow_id = stock.id 
              WHERE borrowed_items.borrow_id = '$borrow_id' AND borrowed_items.stat = 'Borrowed'";

    $check = $db->query($query);

    if ($check->num_rows == 0) {
        die("Item is not currently borrowed.");
    }

    // Fetch product_id
    $borrow_item = $check->fetch_assoc();
    $stock_id = $borrow_item['stock_id'];

    // Update product status to 'Available' (status_id = 1)
    $update = $db->query("UPDATE stock SET status_id = 1 WHERE id = '$stock_id'");

    if (!$update) {
        die("Error updating product status: " . $db->error);
    }

    // Update return date and status in borrowed_items
    $update_borrow = $db->query("UPDATE borrowed_items SET return_date = NOW(), stat = 'Returned' WHERE borrow_id = '$borrow_id'");

    if ($update_borrow) {
        echo "Item returned successfully!";
    } else {
        echo "Error updating borrowed_items: " . $db->error;
    }
}
?>
