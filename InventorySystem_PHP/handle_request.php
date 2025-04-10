<?php
require_once('includes/load.php');
page_require_level(1);

if (isset($_GET['id']) && isset($_GET['action'])) {
  $id = (int)$_GET['id'];
  $action = $_GET['action'];
  $status = ($action === 'approve') ? 'approved' : 'denied';

  $query = "UPDATE item_requests SET status = '{$status}' WHERE id = {$id}";
  if ($db->query($query)) {
    $session->msg("s", "Request has been {$status}.");
  } else {
    $session->msg("d", "Failed to update the request.");
  }
} else {
  $session->msg("d", "Invalid request.");
}
redirect('admin.php');
?>
