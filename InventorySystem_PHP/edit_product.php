<?php
  $page_title = 'Edit product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
?>
<?php
$product = find_by_id('products',(int)$_GET['id']);
$all_categories = find_all('categories');
$all_photo = find_all('media');
if(!$product){
  $session->msg("d","Missing product id.");
  redirect('product.php');
}
?>
<?php
if(isset($_POST['product'])){
  $req_fields = array('product-title', 'product-categorie', 'product-quantity');
  validate_fields($req_fields);

  if(empty($errors)){
      $p_name  = remove_junk($db->escape($_POST['product-title']));
      $p_cat   = (int)$_POST['product-categorie'];
      $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
      $product_id = (int)$_GET['id']; // Ensure we have the correct product ID

      if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
          $media_id = '0';
      } else {
          $media_id = remove_junk($db->escape($_POST['product-photo']));
      }


      // Fetch the current quantity and stock count from the database
      $sql = "SELECT quantity FROM products WHERE id = '{$product_id}'";
      $result = $db->query($sql);
      $product = $db->fetch_assoc($result);

      if (!$product) {
          $session->msg('d', "Product not found!");
          exit(); // Stop execution
      }

      $current_qty = (int)$product['quantity'];

      // Update product details
      $query   = "UPDATE products SET";
      $query  .=" name ='{$p_name}', quantity ='{$p_qty}',";
      $query  .=" categorie_id ='{$p_cat}',media_id='{$media_id}'";
      $query  .=" WHERE id ='{$product_id}'";



      $result = $db->query($query);


      if($db->affected_rows() === 1){
          // Adjust stock based on quantity change
          if ($p_qty > $current_qty) {
              $diff = $p_qty - $current_qty;
              for ($i = 1; $i <= $diff; $i++) {
                  $stock_number = $p_name . "-" . sprintf("%03d", $current_qty + $i);
                  $stock_query = "INSERT INTO stock (product_id, stock_number, status_id) VALUES ({$product_id}, '{$stock_number}', 1)";
                  $db->query($stock_query);

              }
          } elseif ($p_qty < $current_qty) {
              $diff = $current_qty - $p_qty;
              $delete_query = "DELETE FROM stock WHERE product_id = {$product_id} ORDER BY id DESC LIMIT {$diff}";
              $db->query($delete_query);

          }

          $session->msg('s', "Product updated successfully");
          redirect('product.php', false);
      } else {
          $session->msg('d','Sorry, failed to update!');
          exit(); // Stop execution
      }
  } else {
      $session->msg("d", $errors);
      exit(); // Stop execution
  }
}


?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
  <div class="row">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Edit Product</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-7">
           <form method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']);?>">
               </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="product-categorie">
                    <option value=""> Select a categorie</option>
                   <?php  foreach ($all_categories as $cat): ?>
                     <option value="<?php echo (int)$cat['id']; ?>" <?php if($product['categorie_id'] === $cat['id']): echo "selected"; endif; ?> >
                       <?php echo remove_junk($cat['name']); ?></option>
                   <?php endforeach; ?>
                 </select>
                  </div>
                  <div class="col-md-6">
                    <select class="form-control" name="product-photo">
                      <option value=""> No image</option>
                      <?php  foreach ($all_photo as $photo): ?>
                        <option value="<?php echo (int)$photo['id'];?>" <?php if($product['media_id'] === $photo['id']): echo "selected"; endif; ?> >
                          <?php echo $photo['file_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>


              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                  <div class="form-group">
                    <label for="qty">Quantity</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                       <i class="glyphicon glyphicon-shopping-cart"></i>
                      </span>
                      <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>">
                   </div>
                  </div>
                 </div>
               </div>
              </div>
              <button type="submit" name="product" class="btn btn-primary">Update</button>
          </form>
         </div>
        </div>
      </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
