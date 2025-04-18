<?php
// Start output buffering at the very beginning
ob_start();

// Include your load file
require_once('includes/load.php');

// Set proper JSON headers
header('Content-Type: application/json');

// Check for login
if (!$session->isUserLoggedIn()) {
    // Clean any output before sending JSON
    ob_end_clean();
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Check for barcode parameter
if (isset($_GET['barcode'])) {
    $barcode = $db->escape($_GET['barcode']);
    
    $sql = "SELECT p.id, p.name, p.quantity, p.barcode
            FROM products p
            WHERE p.barcode = '{$barcode}' AND p.categorie_id = '12' LIMIT 1";
    
    // Debug SQL query
    // error_log("SQL Query: " . $sql);
    
    $result = $db->query($sql);
    
    if ($db->num_rows($result)) {
        $product = $db->fetch_assoc($result);
        // Clean buffer before sending JSON
        ob_end_clean();
        echo json_encode([
            'id' => (int)$product['id'],
            'name' => $product['name'],
            'quantity' => (int)$product['quantity'],
            'barcode' => $product['barcode'],
        ]);
    } else {
        // Clean buffer before sending JSON
        ob_end_clean();
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    // Clean buffer before sending JSON
    ob_end_clean();
    echo json_encode(['error' => 'No barcode provided']);
}
?>