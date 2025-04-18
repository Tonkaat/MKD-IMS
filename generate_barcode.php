<?php
include 'includes/load.php';

$sql = "SELECT id FROM products WHERE barcode IS NULL OR barcode = ''";
$result = $db->query($sql);

while ($product = $db->fetch_assoc($result)) {
    $barcode = uniqid(); // Or use something like time() . rand(100, 999)
    $id = $product['id'];

    $updateSql = "UPDATE products SET barcode = '{$barcode}' WHERE id = '{$id}'";
    $db->query($updateSql);
}

echo "Barcodes generated successfully.";
?>
