<?php
  $page_title = 'Edit location';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  //Display all locations.
  $location = find_by_id('location',(int)$_GET['id']);
  if(!$location){
    $session->msg("d","Missing location id.");
    redirect('categorie.php');
  }
?>

<?php
if(isset($_POST['edit_loc'])){
  $req_field = array('location-name');
  validate_fields($req_field);
  $loc_name = remove_junk($db->escape($_POST['location-name']));
  if(empty($errors)){
        $sql = "UPDATE location SET name='{$loc_name}'";
       $sql .= " WHERE id='{$location['id']}'";
     $result = $db->query($sql);
     if($result && $db->affected_rows() === 1) {
       $session->msg("s", "Successfully updated Location");
       redirect('categorie.php',false);
     } else {
       $session->msg("d", "Sorry! Failed to Update");
       redirect('categorie.php',false);
     }
  } else {
    $session->msg("d", $errors);
    redirect('categorie.php',false);
  }
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-12 mb-3" style="margin-top: -30px;">
     <a href="javascript:history.back()" class="btn btn-outline-primary">
       <i class="bi bi-arrow-left me-1"></i> Back
     </a>
  </div>
   <div class="col-12">
     <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-6 offset-md-3">
     <div class="card shadow-sm border-0">
       <div class="card-header bg-primary text-white d-flex align-items-center">
         <i class="bi bi-geo-alt-fill me-2"></i>
         <strong>Editing <?php echo remove_junk(ucfirst($location['name'])); ?></strong>
       </div>
       <div class="card-body">
         <form method="post" action="edit_location.php?id=<?php echo (int)$location['id']; ?>">
           <div class="mb-3">
             <label for="location-name" class="form-label">Location Name</label>
             <input type="text" class="form-control" id="location-name" name="location-name" value="<?php echo remove_junk(ucfirst($location['name'])); ?>" required>
           </div>
           <button type="submit" name="edit_loc" class="btn btn-primary w-100">
             <i class="bi bi-check-circle me-1"></i> Update Location
           </button>
         </form>
       </div>
     </div>
   </div>
</div>


<?php include_once('layouts/footer.php'); ?>
