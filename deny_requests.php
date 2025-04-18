<?php
require_once('includes/load.php');

if(isset($_POST['request_id'])) {
  $request_id = (int)$_POST['request_id'];
  
  // Update the request status in the database
  $query = "UPDATE item_requests SET status = 'Denied' WHERE id = {$request_id}";
  if($db->query($query)) {
    $session->msg('s', "Request #$request_id has been denied.");
  } else {
    $session->msg('d', "Failed to deny request: " . $db->error());
  }
  
  // Log the action
  $user_id = $_SESSION['user_id'];
  log_recent_action($user_id, "Denied request #$request_id");
  
  redirect('admin.php', false);
} else {
  $session->msg('d', "No request ID provided.");
  redirect('admin.php', false);
}
?>