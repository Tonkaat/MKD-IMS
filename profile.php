<?php
  $page_title = 'My profile';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);
?>
  <?php
  $user_id = (int)$_GET['id'];
  if(empty($user_id)):
    redirect('home.php',false);
  else:
    $user_p = find_by_id('users',$user_id);
  endif;
?>
<?php include_once('layouts/header.php'); ?>

<div class="col-12 mb-3" style="margin-top: -30px;">
      <a href="javascript:history.back()" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
</div>

<div class="row justify-content-center mt-5">
  <div class="col-md-6 col-lg-4">
    <div class="card shadow-lg border-0 rounded-4">
      <div class="card-body text-center p-4">
        <img 
          src="uploads/users/<?php echo $user_p['image']; ?>" 
          alt="Profile Picture" 
          class="rounded-circle mb-3" 
          style="width: 120px; height: 120px; object-fit: cover;"
        >
        <h4 class="fw-bold mb-3"><?php echo first_character($user_p['name']); ?></h4>

        <?php if ($user_p['id'] === $user['id']): ?>
          <a href="edit_account.php" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-pencil-square me-1"></i> Edit Profile
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
