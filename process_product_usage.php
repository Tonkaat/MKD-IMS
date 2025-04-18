<?php
require_once('includes/load.php');
header('Content-Type: application/json');

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$product_id = (int)$data['product_id'];
$quantity = (int)$data['quantity'];

// Validate quantity
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

// Check if product exists and has enough stock
$product = find_by_id('products', $product_id);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

if ($product['quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
    exit;
}

// Update product quantity
$new_qty = $product['quantity'] - $quantity;
$sql = "UPDATE products SET quantity = ? WHERE id = ?";
$result = $db->query($sql, [$new_qty, $product_id]);

if ($result) {
    // Log the product usage in another table if needed
    $user_id = (int)$_SESSION['user_id'];
    $date = make_date();
    $sql_log = "INSERT INTO product_usage (product_id, user_id, quantity, date) VALUES (?, ?, ?, ?)";
    $db->query($sql_log, [$product_id, $user_id, $quantity, $date]);

    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>