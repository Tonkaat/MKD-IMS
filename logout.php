<?php
  require_once('includes/load.php');
  $user_id = $_SESSION['user_id'];

  // ✅ Update is_logged_in column to 0 when logging out
  $sql = "UPDATE users SET is_logged_in = 0 WHERE id = '$user_id'";
  if (!$db->query($sql)) {
      echo "Error updating logout status: " . $db->error; // Debugging output
  }
  
  $session->logout();
  redirect('index.php');
?>
