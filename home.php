<?php
  $page_title = 'User Dashboard';
  require_once('includes/load.php');
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
  
  // Get current user's ID
  $current_user_id = $_SESSION['user_id'];
  
  // Fetch this user's requests
  $user_requests = find_by_sql("SELECT r.*, c.name as category_name 
                               FROM item_requests r 
                               LEFT JOIN categories c ON r.categorie_id = c.id 
                               WHERE r.user_id = '{$current_user_id}' 
                               ORDER BY r.request_date DESC 
                               LIMIT 10");
?>
<?php include_once('layouts/header.php'); ?>
<div class="container py-4">
  <!-- Welcome Section -->
  <div class="row mb-4">
    <div class="col-12">
      <?= display_msg($msg); ?>
      <div class="card shadow-sm border-0">
        <div class="card-body text-center">
          <h1 class="display-5 fw-semibold mb-2 text-primary">Welcome, <?= ucfirst($user['name']); ?>!</h1>
          <p class="lead mb-1">MKD Inventory Management System</p>
          <p class="text-muted">You are the user for this location </p>
          <button class="btn btn-primary btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#requestModal">
            <i class="bi bi-plus-circle me-1"></i> Request New Item
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Request Status Notifications -->
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex align-items-center">
          <i class="bi bi-bell fs-5 me-2 text-primary"></i>
          <h5 class="mb-0 text-primary fw-bold">Your Request Status</h5>
        </div>
        <div class="card-body p-0">
          <?php if (empty($user_requests)): ?>
            <div class="p-4 text-center text-muted">
              <i class="bi bi-inbox-fill fs-1"></i>
              <p class="mt-2 mb-0">You haven't made any requests yet.</p>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Request Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($user_requests as $request): ?>
                    <tr>
                      <td><?= remove_junk(ucfirst($request['item_name'])); ?></td>
                      <td><?= remove_junk(ucfirst($request['category_name'])); ?></td>
                      <td><?= (int)$request['quantity']; ?></td>
                      <td>
                        <?php if ($request['status'] == 'Pending'): ?>
                          <span class="badge bg-warning text-dark">Pending</span>
                        <?php elseif ($request['status'] == 'Approved'): ?>
                          <span class="badge bg-info">Approved</span>
                        <?php elseif ($request['status'] == 'Added'): ?>
                          <span class="badge bg-success">Added to Inventory</span>
                        <?php elseif ($request['status'] == 'Denied'): ?>
                          <span class="badge bg-danger">Denied</span>
                        <?php else: ?>
                          <span class="badge bg-secondary"><?= $request['status']; ?></span>
                        <?php endif; ?>
                      </td>
                      <td><?= read_date($request['request_date']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Request New Item -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="submit_request.php" method="POST" class="modal-content border-0 shadow-sm">
      <div class="modal-header">
        <h5 class="modal-title" id="requestModalLabel">Request New Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="item_name" required>
        </div>

        <div class="mb-3">
          <label for="categorie_id" class="form-label">Category</label>
          <select class="form-select" name="categorie_id" required>
            <option value="">Select a Category</option>
            <?php
              $categories = find_all('categories');
              foreach ($categories as $cat):
            ?>
              <option value="<?= (int)$cat['id']; ?>"><?= remove_junk($cat['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="quantity" class="form-label">Quantity</label>
          <input type="number" class="form-control" name="quantity" value="1" min="1">
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Submit Request</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Add JavaScript to refresh notifications periodically if desired -->
<script>
  // Refresh the page every 5 minutes to update notification status
  // Uncomment if you want automatic refreshing
  /*
  setTimeout(function() {
    window.location.reload();
  }, 300000); // 5 minutes in milliseconds
  */
</script>
<?php include_once('layouts/footer.php'); ?>