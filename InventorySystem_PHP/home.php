<?php
  $page_title = 'Home Page';
  require_once('includes/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>

  <div class="col-md-12">
    <div class="panel">
      <div class="jumbotron text-center">
        <h1>Welcome User <hr> Inventory Management System</h1>
        <p>Browse around to find out the pages that you can access!</p>
        <button class="btn btn-primary mt-3" data-toggle="modal" data-target="#requestModal">
          <i class="glyphicon glyphicon-plus"></i> Request New Item
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Item Request -->
<div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel">
  <div class="modal-dialog" role="document">
    <form action="submit_request.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="requestModalLabel">Request New Item</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="item_name">Item Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="item_name" required>
        </div>
      <div class="form-group">
        <label for="categorie_id">Category</label>
        <select class="form-control" name="categorie_id" required>
          <option value="">Select a Category</option>
          <?php
            $categories = find_all('categories'); // Assuming this fetches your categories
            foreach ($categories as $cat):
          ?>
            <option value="<?= (int)$cat['id']; ?>"><?= remove_junk($cat['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

        <div class="form-group">
          <label for="quantity">Quantity</label>
          <input type="number" class="form-control" name="quantity" value="1" min="1">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Submit Request</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
