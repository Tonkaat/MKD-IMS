<?php
  require_once('includes/load.php');
  // Checkin what level user has permission to view this page
  page_require_level(1);
?>

<?php
  // Check if an 'id' is passed in the URL for the user to delete
  if (isset($_GET['id'])) {
    // Get the user ID from the URL
    $user_id = (int)$_GET['id'];

    // Perform the deletion action if the ID exists
    $delete_id = delete_by_id('users', $user_id);
    if ($delete_id) {
        // Successfully deleted user
        $session->msg("s", "User deleted.");
        redirect('users.php');
    } else {
        // Failed to delete user or missing permission
        $session->msg("d", "User deletion failed or Missing permission.");
        redirect('users.php');
    }
  } else {
    // If no 'id' is passed, redirect with an error
    $session->msg("d", "Invalid request.");
    redirect('users.php');
  }
?>
