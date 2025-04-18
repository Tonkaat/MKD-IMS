<?php
  $page_title = 'Edit User';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
  $e_user = find_by_id('users',(int)$_GET['id']);
  $groups  = find_all('user_groups');
  $locations = find_all('location');
  if(!$e_user){
    $session->msg("d","Missing user id.");
    redirect('users.php');
  }
?>

<?php
//Update User basic info
  if(isset($_POST['update'])) {
    $req_fields = array('name','username','level');
    validate_fields($req_fields);
    if(empty($errors)){
             $id = (int)$e_user['id'];
           $name = remove_junk($db->escape($_POST['name']));
       $username = remove_junk($db->escape($_POST['username']));
          $level = (int)$db->escape($_POST['level']);
       $status   = remove_junk($db->escape($_POST['status']));
       $location = (int)$db->escape($_POST['location']);
            $sql = "UPDATE users SET name ='{$name}', username ='{$username}',user_level='{$level}', location_id = '{$location}', status='{$status}' WHERE id='{$db->escape($id)}'";
         $result = $db->query($sql);
          if($result && $db->affected_rows() === 1){
            $session->msg('s',"Acount Updated ");
            redirect('edit_user.php?id='.(int)$e_user['id'], false);
          } else {
            $session->msg('d',' Sorry failed to updated!');
            redirect('edit_user.php?id='.(int)$e_user['id'], false);
          }
    } else {
      $session->msg("d", $errors);
      redirect('edit_user.php?id='.(int)$e_user['id'],false);
    }
  }
?>
<?php
// Update user password
if(isset($_POST['update-pass'])) {
  $req_fields = array('password');
  validate_fields($req_fields);
  if(empty($errors)){
           $id = (int)$e_user['id'];
     $password = remove_junk($db->escape($_POST['password']));
     $h_pass   = sha1($password);
          $sql = "UPDATE users SET password='{$h_pass}' WHERE id='{$db->escape($id)}'";
       $result = $db->query($sql);
        if($result && $db->affected_rows() === 1){
          $session->msg('s',"User password has been updated ");
          redirect('edit_user.php?id='.(int)$e_user['id'], false);
        } else {
          $session->msg('d',' Sorry failed to updated user password!');
          redirect('edit_user.php?id='.(int)$e_user['id'], false);
        }
  } else {
    $session->msg("d", $errors);
    redirect('edit_user.php?id='.(int)$e_user['id'],false);
  }
}

?>
<?php include_once('layouts/header.php'); ?>
<div class="col-12 mb-3" style="margin-top: -30px;">
      <a href="javascript:history.back()" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
</div>
<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
      <?php echo display_msg($msg); ?>
    </div>
  </div>

  <!-- Edit User Form -->
  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <strong>Update <?php echo remove_junk(ucwords($e_user['name'])); ?> Account</strong>
        </div>
        <div class="card-body">
          <form method="post" action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" class="clearfix">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" name="name" value="<?php echo remove_junk(ucwords($e_user['name'])); ?>" required>
            </div>
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" name="username" value="<?php echo remove_junk(ucwords($e_user['username'])); ?>" required>
            </div>
            <div class="mb-3">
              <label for="level" class="form-label">User Role</label>
              <select class="form-select" name="level" required>
                <?php foreach ($groups as $group ):?>
                  <option <?php if($group['group_level'] === $e_user['user_level']) echo 'selected="selected"';?> value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
                <?php endforeach;?>
              </select>
            </div>
            <div class="mb-3">
              <label for="location" class="form-label">Location</label>
              <select class="form-select" name="location" required>
                <?php foreach ($locations as $location): ?>
                  <option value="<?php echo $location['id']; ?>" 
                    <?php if($location['name'] === $e_user['location_id']) echo 'selected="selected"'; ?>>
                    <?php echo ucwords($location['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" name="status" required>
                <option <?php if($e_user['status'] === '1') echo 'selected="selected"';?> value="1">Active</option>
                <option <?php if($e_user['status'] === '0') echo 'selected="selected"';?> value="0">Deactive</option>
              </select>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" name="update" class="btn btn-primary">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Change Password Form -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-danger text-white">
          <strong>Change <?php echo remove_junk(ucwords($e_user['name'])); ?> Password</strong>
        </div>
        <div class="card-body">
          <form action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" method="post" class="clearfix">
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" name="password" placeholder="Enter new password" required>
            </div>
            <div class="d-grid gap-2">
              <button type="submit" name="update-pass" class="btn btn-danger">Change Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
