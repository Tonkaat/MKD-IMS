<?php
  $page_title = 'Add User';
  require_once('includes/load.php');
  // Check user permission
  page_require_level(1);

  $groups = find_all('user_groups');
  $locations = find_all('location'); // Fetch all locations from DB
?>

<?php
  if(isset($_POST['add_user'])) {

   $req_fields = array('full-name', 'username', 'password', 'level', 'location');
   validate_fields($req_fields);

   if(empty($errors)) {
       $name       = remove_junk($db->escape($_POST['full-name']));
       $username   = remove_junk($db->escape($_POST['username']));
       $password   = remove_junk($db->escape($_POST['password']));
       $user_level = (int)$db->escape($_POST['level']);
       $location   = (int)$db->escape($_POST['location']); // Get selected location

       $password = sha1($password);

       $query = "INSERT INTO users (name, username, password, user_level, location_id, status) ";
       $query .= "VALUES ('{$name}', '{$username}', '{$password}', '{$user_level}', '{$location}', '1')";

       if($db->query($query)) {
          $session->msg('s',"User account has been created!");
          redirect('add_user.php', false);
       } else {
          $session->msg('d','Sorry, failed to create account!');
          redirect('add_user.php', false);
       }
   } else {
      $session->msg("d", $errors);
      redirect('add_user.php', false);
   }
 }
?>

<?php include_once('layouts/header.php'); ?>
<?php echo display_msg($msg); ?>

<div class="col-12 mb-3" style="margin-top: -30px;">
      <a href="javascript:history.back()" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
</div>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-person-plus-fill"></i> Add New User</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="add_user.php">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full-name" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">User Role</label>
                            <select class="form-select" name="level" required>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?php echo $group['group_level']; ?>"><?php echo ucwords($group['group_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Assign Location</label>
                            <select class="form-select" name="location" required>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?php echo $location['id']; ?>"><?php echo ucwords($location['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" name="add_user" class="btn btn-primary rounded-pill">
                                <i class="bi bi-person-plus me-2"></i> Add User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include_once('layouts/footer.php'); ?>
