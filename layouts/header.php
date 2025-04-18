<?php $user = current_user(); ?>
<!DOCTYPE html>
  <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title><?php if (!empty($page_title))
           echo remove_junk($page_title);
            elseif(!empty($user))
           echo ucfirst($user['name']);
            else echo "Inventory Management System";?>
    </title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <link rel="stylesheet" href="libs/css/main.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  </head>
  <body>
  <?php  if ($session->isUserLoggedIn(true)): ?>
    <header id="header" class="bg-white shadow-sm px-4 py-3 mb-4 d-flex justify-content-between align-items-center">
  <!-- Logo -->
  <div class="d-flex align-items-center">
    <i class="bi bi-box-seam-fill fs-4 text-primary me-2"></i>
    <span class="fs-4 fw-bold text-dark">Inventory System</span>
  </div>

  <!-- Date and Time -->
  <div class="text-muted me-auto ms-5 d-none d-md-block">
    <strong>
      <?php 
        date_default_timezone_set('Asia/Manila');
        echo date("F j, Y, g:i a"); 
      ?>
    </strong>
  </div>

  <!-- User Profile Dropdown -->
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="uploads/users/<?php echo $user['image'];?>" alt="user-image" class="rounded-circle me-2" width="35" height="35">
      <span class="fw-semibold text-dark"><?php echo remove_junk(ucfirst($user['name'])); ?></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2">
      <li>
        <a class="dropdown-item" href="profile.php?id=<?php echo (int)$user['id']; ?>">
          <i class="bi bi-person-circle me-2"></i> Profile
        </a>
      </li>
      <!-- <li>
        <a class="dropdown-item" href="edit_account.php">
          <i class="bi bi-gear me-2"></i> Settings
        </a>
      </li> -->
      <li>
        <hr class="dropdown-divider">
      </li>
      <li>
        <a class="dropdown-item text-danger" href="logout.php">
          <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
      </li>
    </ul>
  </div>
</header>
  </header>
    <div class="sidebar">
      <?php if($user['user_level'] === '1'): ?>
        <?php include_once('admin_menu.php');?>
      <?php elseif($user['user_level'] === '2'): ?>
        <?php include_once('special_menu.php');?>
      <?php elseif($user['user_level'] === '3'): ?>
        <?php include_once('user_menu.php');?>
      <?php endif;?>
    </div>
<?php endif;?>

  <!-- Main Page -->
  <div class="page">
    <div class="container-fluid">
