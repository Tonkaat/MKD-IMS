<?php
// Prevent any unwanted output
ob_start();

// Include your load file
require_once('includes/load.php');

// Set proper JSON headers
header('Content-Type: application/json');

// Check for login
if (!$session->isUserLoggedIn()) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get JSON data from POST request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Debug incoming data
error_log("Received data: " . print_r($data, true));

// Check if data is valid
if (!$data || !isset($data['items']) || empty($data['items'])) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'No items provided'
    ]);
    exit;
}

// Add this to check your database connection
if (!$db) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error'
    ]);
    exit;
}

try {
    // Start transaction if your database class supports it
    if (method_exists($db, 'begin_transaction')) {
        $db->begin_transaction();
    }
    
    $processed_count = 0;
    $errors = [];
    
    // Process each item
    foreach ($data['items'] as $item) {
        $product_id = (int)$item['product_id'];
        $quantity = (int)$item['quantity'];
        
        if ($product_id <= 0 || $quantity <= 0) {
            $errors[] = "Invalid product ID or quantity";
            continue;
        }
        
        // Get current stock and product name
        $current_stock_query = $db->query("SELECT name, quantity FROM products WHERE id = {$product_id} LIMIT 1");
        
        if (!$current_stock_query || $db->num_rows($current_stock_query) === 0) {
            $errors[] = "Product ID {$product_id} not found";
            continue;
        }
        
        $product = $db->fetch_assoc($current_stock_query);
        $current_stock = (int)$product['quantity'];
        $product_name = $product['name'];
        
        // Check if enough stock available
        if ($current_stock < $quantity) {
            $errors[] = "Not enough stock for product ID {$product_id}";
            continue;
        }
        
        // Update product quantity
        $new_quantity = $current_stock - $quantity;
        $update_sql = "UPDATE products SET quantity = {$new_quantity} WHERE id = {$product_id}";
        $update_result = $db->query($update_sql);
        
        if (!$update_result) {
            $errors[] = "Failed to update product ID {$product_id}";
            continue;
        }
        
        // Remove stock entries when decreasing quantity
        if ($quantity > 0) {
            $delete_query = "DELETE FROM stock WHERE product_id = {$product_id} ORDER BY id DESC LIMIT {$quantity}";
            $stock_result = $db->query($delete_query);
            
            if (!$stock_result) {
                $errors[] = "Failed to update stock records for product ID {$product_id}";
                continue;
            }
        }
        
        // Log the usage in product_usage table
        $user_id = (int)$session->user_id;
        $date = date('Y-m-d H:i:s');
        
        // Check if product_usage table exists
        $check_table = $db->query("SHOW TABLES LIKE 'product_usage'");
        if ($db->num_rows($check_table) === 0) {
            // Create table if it doesn't exist
            $create_table_sql = "CREATE TABLE IF NOT EXISTS `product_usage` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `product_id` int(11) NOT NULL,
                `quantity` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `date` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `product_id` (`product_id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            
            $db->query($create_table_sql);
        }
        
        $log_sql = "INSERT INTO product_usage 
                   (product_id, quantity, user_id, date) 
                   VALUES 
                   ({$product_id}, {$quantity}, {$user_id}, '{$date}')";
        $log_result = $db->query($log_sql);
        
        if (!$log_result) {
            $errors[] = "Failed to log usage for product ID {$product_id}";
            continue;
        }
        
        $processed_count++;
    }
    
    // If there were errors with any items, rollback the transaction
    if (!empty($errors)) {
        if (method_exists($db, 'rollback')) {
            $db->rollback();
        }
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Errors processing items: ' . implode(', ', $errors)
        ]);
        exit;
    }
    
    // If everything was successful, commit the transaction
    if (method_exists($db, 'commit')) {
        $db->commit();
    }
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'All items processed successfully',
        'total_items' => $processed_count
    ]);
    
} catch (Exception $e) {
    // If any exception occurs, rollback the transaction
    if (method_exists($db, 'rollback')) {
        $db->rollback();
    }
    
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error processing items: ' . $e->getMessage()
    ]);
}
?>