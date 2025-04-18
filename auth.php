<?php include_once('includes/load.php'); ?>
<?php
$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

if(empty($errors)){
  $user_id = authenticate($username, $password);
  if($user_id){
    // Create session with ID
    $session->login($user_id);

    // Update Sign-in time
    updateLastLogIn($user_id);

    // ✅ Update is_logged_in column to 1  
    $sql = "UPDATE users SET is_logged_in = 1 WHERE id = '$user_id'";
    if (!$db->query($sql)) {
        echo "Error updating login status: " . $db->error; // Debugging output
    }

    $session->msg("s", "Welcome to Inventory Management System");
    redirect('admin.php', false);

  } else {
    $session->msg("d", "Sorry Username/Password incorrect.");
    redirect('index.php',false);
  }

} else {
   $session->msg("d", $errors);
   redirect('index.php',false);
}

?>
