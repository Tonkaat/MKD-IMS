<?php
require_once('includes/load.php');
$id = (int)$_GET['id'];
$sql = "UPDATE item_requests SET status = 'Approved' WHERE id = '{$id}'";
$db->query($sql);
header("Location: admin.php?status=success&message=Request approved.");
?>
