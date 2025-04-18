<?php
  $page_title = 'Edit categorie';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  //Display all catgories.
  $categorie = find_by_id('categories',(int)$_GET['id']);
  if(!$categorie){
    $session->msg("d","Missing categorie id.");
    redirect('categorie.php');
  }
?>

<?php
if(isset($_POST['edit_cat'])){
  $req_field = array('categorie-name');
  validate_fields($req_field);
  $cat_name = remove_junk($db->escape($_POST['categorie-name']));
  if(empty($errors)){
        $sql = "UPDATE categories SET name='{$cat_name}'";
       $sql .= " WHERE id='{$categorie['id']}'";
     $result = $db->query($sql);
     if($result && $db->affected_rows() === 1) {
       $session->msg("s", "Successfully updated Categorie");
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
         <i class="bi bi-folder me-2"></i>
         <strong>Editing <?php echo remove_junk(ucfirst($categorie['name'])); ?></strong>
       </div>
       <div class="card-body">
         <form method="post" action="edit_categorie.php?id=<?php echo (int)$categorie['id']; ?>">
           <div class="mb-3">
             <label for="categorie-name" class="form-label">Category Name</label>
             <input type="text" class="form-control" id="categorie-name" name="categorie-name" value="<?php echo remove_junk(ucfirst($categorie['name'])); ?>" required>
           </div>
           <button type="submit" name="edit_cat" class="btn btn-primary w-100">
             <i class="bi bi-check-circle me-1"></i> Update Category
           </button>
         </form>
       </div>
     </div>
   </div>
</div>



<?php include_once('layouts/footer.php'); ?>
