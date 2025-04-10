<?php
  require_once('includes/load.php');
  if (isset($_POST['make_available'])) {
    $req_fields = array('product-title','product-categorie','product-quantity');
    validate_fields($req_fields);
    if (empty($errors)) {
      $p_name  = remove_junk($db->escape($_POST['product-title']));
      $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
      $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
      $location_id = isset($_POST['product-location']) ? remove_junk($db->escape($_POST['product-location'])) : '';
      $req_id  = (int)$_POST['request_id'];
      if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
        $media_id = '0';
      } else {
        $media_id = remove_junk($db->escape($_POST['product-photo']));
      }
      $date = make_date();
      // Changed field names to match the table structure
      $query = "INSERT INTO products (name, quantity, categorie_id, media_id, date, location_id) VALUES (
        '{$p_name}', '{$p_qty}', '{$p_cat}', '{$media_id}', '{$date}', '{$location_id}')";
      $query .= " ON DUPLICATE KEY UPDATE name='{$p_name}'";
      if ($db->query($query)) {
        $product_id = $db->insert_id;
        if (!$product_id) {
            $session->msg('d', 'Error: Product ID not retrieved!');
            redirect('admin.php', false);
        }
        
        $qty = (int)$p_qty;
        $stock_success = true;
        
        for ($i = 1; $i <= $qty; $i++) {
            $stock_number = $p_name . "-" . sprintf("%03d", $i);
            $stock_query = "INSERT INTO stock (product_id, stock_number, status_id) VALUES ({$product_id}, '{$stock_number}', 1)";
            
            if (!$db->query($stock_query)) {
                $stock_success = false;
                // Log the error or add it to a debug variable
                $stock_error = $db->error();
                break;
            }
        }
        
        if (!$stock_success) {
            $session->msg('d', 'Product added but failed to create stock items: ' . $stock_error);
            redirect('admin.php', false);
        }
        $user_id = $_SESSION['user_id'];

        // Update the item_requests table to mark as fulfilled
        $status_update = $db->query("UPDATE item_requests SET status = 'Added', added_to_inventory = 1 WHERE id = {$req_id}");
        if (!$status_update) {
            $session->msg('d', 'Product and stock added but failed to update request status: ' . $db->error());
            redirect('admin.php', false);
        }
        $session->msg('s', "Request approved and product '{$p_name}' added with {$qty} stock items.");
        redirect('admin.php', false);
      } else {
        $session->msg('d', 'Sorry, failed to add item.');
        redirect('admin.php', false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('admin.php', false);
    }
  }
?>