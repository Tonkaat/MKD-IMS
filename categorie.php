<?php
$page_title = 'All categories and locations';
require_once('includes/load.php');
page_require_level(1);

$all_categories = find_all('categories');
$all_locations = find_all('location'); // Fetch locations

// Get the type (category or location) and its corresponding name from the URL parameters
$type = isset($_POST['type']) ? $_POST['type'] : '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Fetch the name of the selected category or location
$type_name = '';
if ($type === 'category') {
    $category = find_by_id('categories', $id);
    $type_name = $category ? $category['name'] : '';
} elseif ($type === 'location') {
    $location = find_by_id('location', $id);
    $type_name = $location ? $location['name'] : '';
}
?>

<?php include_once('layouts/header.php'); ?>

<!-- Page header -->
<div class="row mb-3">
  <div class="col-12">
    <h2 class="fw-bold text-primary ">Item Categorization</h2>
    <input type="text" id="search-bar" class="form-control mb-2 col-md-4" placeholder="Search items by name...">
  </div>
</div>

<!-- Success/Error Message Box -->
<?php if ($session->has_msg()): ?>
  <div class="alert alert-<?= $session->msg_type(); ?> alert-dismissible fade show" role="alert">
    <?= $session->msg(); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="row">
  <!-- Add Category/Location -->
  <div class="col-md-3 mb-3">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-primary text-white fw-semibold">
        <i class="bi bi-plus-circle me-1"></i> Add Category or Location
      </div>
      <div class="card-body">
        <form action="add_category_location.php" method="POST">
          <div class="mb-3">
            <label for="type" class="form-label">Add as:</label>
            <select name="type" id="type" class="form-select" required>
              <option value="category">Category</option>
              <option value="location">Location</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Add</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Categories and Locations -->
  <div class="col-md-9">
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-light fw-semibold">
        <i class="bi bi-grid-3x3-gap-fill me-1"></i> Categories
      </div>
      <div class="card-body">
        <table class="table table-hover table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Category Name</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_categories as $cat): ?>
              <tr>
                <td class="text-center"><?= count_id(); ?></td>
                <td>
                  <a href="#" class="text-decoration-none open-modal" data-type="category" data-id="<?= $cat['id']; ?>">
                    <?= remove_junk(ucfirst($cat['name'])); ?>
                  </a>
                  <a href="edit_categorie.php?id=<?= (int)$cat['id']; ?>" class="btn btn-sm btn-primary ms-2" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <a href="delete_categorie.php?id=<?= (int)$cat['id']; ?>" class="btn btn-sm btn-danger" title="Remove">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-header bg-light fw-semibold">
        <i class="bi bi-geo-alt-fill me-1"></i> Locations
      </div>
      <div class="card-body">
        <table class="table table-hover table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Location Name</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_locations as $loc): ?>
              <tr>
                <td class="text-center"><?= count_id(); ?></td>
                <td>
                  <a href="#" class="text-decoration-none open-modal" data-type="location" data-id="<?= $loc['id']; ?>">
                    <?= remove_junk(ucfirst($loc['name'])); ?>
                  </a>
                  <a href="edit_location.php?id=<?= (int)$loc['id']; ?>" class="btn btn-sm btn-primary ms-2" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <a href="delete_locations.php?id=<?= (int)$loc['id']; ?>" class="btn btn-sm btn-danger" title="Remove">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Items Modal -->
<div class="modal fade" id="itemsModal" tabindex="-1" aria-labelledby="itemsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="itemsModalLabel">Items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="modal-search-bar" class="form-control mb-3" placeholder="Search items by name...">
        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Stock Number</th>
              <th>Product Name</th>
              <th>Location</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="modal-content">
            <tr>
              <td colspan="5" class="text-center">Loading...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('search-bar').addEventListener('input', function() {
    var searchTerm = this.value.toLowerCase();
    var rows = document.querySelectorAll('table tbody tr');
    
    rows.forEach(function(row) {
        var productName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        if (productName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Search function for filtering the modal items
document.getElementById('modal-search-bar').addEventListener('input', function() {
    var searchTerm = this.value.toLowerCase();
    var rows = document.querySelectorAll('#modal-content tr');
    
    rows.forEach(function(row) {
        var productName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        if (productName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php include_once('layouts/footer.php'); ?>
