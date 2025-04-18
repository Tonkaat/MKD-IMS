<?php
require_once('includes/load.php');
page_require_level(3); // Ensure it's a logged-in user

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_SESSION['user_id'];
    $item_name = $db->escape($_POST['item_name']);
    $categorie_id = (int)$_POST['categorie_id']; // New field from the dropdown
    $quantity = (int)$_POST['quantity'];

    $sql = "INSERT INTO item_requests (user_id, item_name, categorie_id, quantity) 
            VALUES ('{$user_id}', '{$item_name}', '{$categorie_id}', '{$quantity}')";

    if ($db->query($sql)) {
        header("Location: home.php?status=success&message=Request submitted successfully.");
    } else {
        header("Location: home.php?status=error&message=Failed to submit request.");
    }
}
?>
