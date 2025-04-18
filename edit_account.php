<?php
  $page_title = 'Edit Account';
  require_once('includes/load.php');
   page_require_level(3);
?>
<?php
//update user image
  if(isset($_POST['submit'])) {
  $photo = new Media();
  $user_id = (int)$_POST['user_id'];
  $photo->upload($_FILES['file_upload']);
  if($photo->process_user($user_id)){
    $session->msg('s','photo has been uploaded.');
    redirect('edit_account.php');
    } else{
      $session->msg('d',join($photo->errors));
      redirect('edit_account.php');
    }
  }
?>
<?php
 //update user other info
  if(isset($_POST['update'])){
    $req_fields = array('name','username' );
    validate_fields($req_fields);
    if(empty($errors)){
             $id = (int)$_SESSION['user_id'];
           $name = remove_junk($db->escape($_POST['name']));
       $username = remove_junk($db->escape($_POST['username']));
            $sql = "UPDATE users SET name ='{$name}', username ='{$username}' WHERE id='{$id}'";
    $result = $db->query($sql);
          if($result && $db->affected_rows() === 1){
            $session->msg('s',"Acount updated ");
            redirect('edit_account.php', false);
          } else {
            $session->msg('d',' Sorry failed to updated!');
            redirect('edit_account.php', false);
          }
    } else {
      $session->msg("d", $errors);
      redirect('edit_account.php',false);
    }
  }
?>
<?php include_once('layouts/header.php'); ?>

<div class="col-12 mb-3" style="margin-top: -30px;">
      <a href="javascript:history.back()" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
</div>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>

  <!-- Profile Image Update -->
  <div class="col-md-6">
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light fw-bold">
        <i class="bi bi-camera me-2"></i>Change My Photo
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="me-4">
            <img 
              src="uploads/users/<?php echo $user['image'];?>" 
              alt="User Photo" 
              class="rounded-circle" 
              style="width: 100px; height: 100px; object-fit: cover;"
            >
          </div>
          <form class="flex-grow-1" action="edit_account.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <input type="file" name="file_upload" id="file_upload" class="d-none">
              <label for="file_upload" class="btn btn-outline-primary rounded-pill">
                <i class="bi bi-upload me-1"></i> Choose File
              </label>
              <span id="file-chosen" class="ms-2 text-muted">No file chosen</span>
            </div>
            <input type="hidden" name="user_id" value="<?php echo $user['id'];?>">
            <button type="submit" name="submit" class="btn btn-primary rounded-pill">Change</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Account Info -->
  <div class="col-md-6">
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light fw-bold">
        <i class="bi bi-pencil-square me-2"></i>Edit My Account
      </div>
      <div class="card-body">
        <form method="post" action="edit_account.php?id=<?php echo (int)$user['id'];?>" class="clearfix">
          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="name" value="<?php echo remove_junk(ucwords($user['name'])); ?>">
          </div>
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?php echo remove_junk(ucwords($user['username'])); ?>">
          </div>
          <div class="d-flex justify-content-between">
            <a href="change_password.php" class="btn btn-outline-danger rounded-pill">Change Password</a>
            <button type="submit" name="update" class="btn btn-info rounded-pill text-white">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const fileInput = document.getElementById('file_upload');
  const fileChosen = document.getElementById('file-chosen');

  fileInput.addEventListener('change', function(){
    fileChosen.textContent = this.files[0]?.name || 'No file chosen';
  });
</script>

<?php include_once('layouts/footer.php'); ?>
