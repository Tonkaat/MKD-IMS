<?php
$page_title = 'Inventory by Location';
require_once('includes/load.php');
page_require_level(3);

// Get the logged-in user's location_id
$user = current_user(); // Make sure this function is correctly defined in `load.php`

// Ensure $user is properly set before accessing properties
$user_location_id = isset($user['location_id']) ? $user['location_id'] : '';

// Get location name for display purposes
$location_name = 'Unknown Location';
if (!empty($user_location_id)) {
    $location_result = $db->query("SELECT name FROM location WHERE id = '{$db->escape($user_location_id)}'");
    if ($location_result && $location_result->num_rows > 0) {
        $location_data = $location_result->fetch_assoc();
        $location_name = $location_data['name'];
    }
}

$all_locations = find_all('location');
$all_categories = find_all('categorie');

// Fetch categories safely
$categories = $db->query("SELECT * FROM categories");

if (!$categories) {
    die("Query failed: " . $db->error);
}

$categories = $categories->fetch_all(MYSQLI_ASSOC); // Fetch results as an associative array

// Check if 'location_id' exists in 'stock' table before querying
$location_check = $db->query("SHOW COLUMNS FROM stock LIKE 'location_id'");

if ($location_check && $location_check->num_rows > 0) {
    $locations = $db->query("SELECT DISTINCT s.location_id, l.name 
                            FROM stock s
                            JOIN location l ON s.location_id = l.id");

    if (!$locations) {
        die("Query failed: " . $db->error);
    }

    $locations = $locations->fetch_all(MYSQLI_ASSOC);
} else {
    $locations = []; // If 'location_id' column doesn't exist, return an empty array
}

// Fetch products based on the user's location - only products with stock > 0
$products = [];
if (!empty($user_location_id)) {
    $sql = "SELECT products.id, 
                   products.name AS product_name,
                   COUNT(stock.id) AS stock_count, 
                   categories.name AS categorie
            FROM products
            JOIN stock ON products.id = stock.product_id AND stock.location_id = '{$db->escape($user_location_id)}'
            JOIN categories ON products.categorie_id = categories.id
            GROUP BY products.id
            HAVING stock_count > 0";

    $result = $db->query($sql);
    if ($result) {
        $products = $db->while_loop($result);
    } else {
        // Handle query error
        $error = $db->error;
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<div class="container mt-4">
<h2 class="mb-0 fw-bold text-primary">Inventory in <?= htmlspecialchars($location_name) ?></h2>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <input type="text" class="form-control w-50" id="search" placeholder="Search" onkeyup="filterProducts()">
  </div>

  <?php if (isset($_GET['status'], $_GET['message'])): ?>
    <div class="alert alert-<?= $_GET['status'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_GET['message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (empty($products)): ?>
    <div class="alert alert-warning text-center">No products with inventory found at this location.</div>
  <?php elseif (isset($error)): ?>
    <div class="alert alert-danger">Error loading products: <?= htmlspecialchars($error) ?></div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th scope="col">Product Name</th>
            <th scope="col">Category</th>
            <th scope="col">Quantity</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product): ?>
          <tr class="product-row">
            <td onclick="toggleStock(<?= $product['id'] ?>)" style="cursor: pointer;">
              <?= htmlspecialchars($product['product_name']) ?>
            </td>
            <td><?= htmlspecialchars($product['categorie']) ?></td>
            <td><?= htmlspecialchars($product['stock_count']) ?></td>
          </tr>

          <tr id="stock-<?= $product['id'] ?>" class="stock-details" style="display:none;">
            <td colspan="3">
              <table class="table table-sm table-striped">
                <thead>
                  <tr>
                    <th>Stock ID</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $stockQuery = "SELECT stock.id, stock.stock_number, status.name AS status_name
                                   FROM stock
                                   LEFT JOIN status ON stock.status_id = status.id
                                   WHERE stock.product_id = {$product['id']} 
                                   AND stock.location_id = '{$db->escape($user_location_id)}'";
                    $stockResult = $db->query($stockQuery);
                    
                    if ($stockResult && $stockResult->num_rows > 0):
                      while ($stock = $stockResult->fetch_assoc()): ?>
                        <tr>
                          <td><?= htmlspecialchars($stock['stock_number']) ?></td>
                          <td><?= htmlspecialchars($stock['status_name']) ?></td>
                          <td>
                            <button class="btn btn-warning btn-sm" onclick="openReportModal(<?= $stock['id'] ?>)">Report Missing/Lost</button>
                            <?php if ($stock['status_name'] === 'Missing'): ?>
                              <button class="btn btn-success btn-sm" onclick="confirmFound(<?= $stock['id'] ?>)">Found</button>
                            <?php endif; ?>
                            <?php if ($stock['status_name'] === 'Lost'): ?>
                              <button class="btn btn-primary btn-sm">Request</button>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endwhile;
                    else: ?>
                      <tr><td colspan="3">No stock items found at this location.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Report Missing/Lost Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="reportForm" action="report_missing.php" method="POST">
      <div class="modal-header">
        <h5 class="modal-title" id="reportModalLabel">Report Item as Missing or Lost</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="stockIdReport" name="stock_id">
        <label for="status" class="form-label">Choose Status:</label>
        <select id="status" name="status" class="form-select" required>
          <option value="">Select Status</option>
          <option value="3">Missing</option>
          <option value="4">Lost</option>
        </select>
        <div id="reportMessage" class="mt-3 text-success fw-bold d-none"></div>
        <button id="availableAgainBtn" type="button" class="btn btn-success mt-2 d-none"
                onclick="markAsAvailable(document.getElementById('stockIdReport').value)">
          Report as Available Again
        </button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Submit Report</button>
      </div>
    </form>
  </div>
</div>

<style>


  /* Search and Button Layout */
  .form-control {
    border-radius: 10px;
  }

  .btn {
    border-radius: 10px;
  }

  /* Table Styling */
  .table-striped tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
  }

  .table-dark th {
    background-color: #343a40;
    color: white;
  }

  .btn-sm {
    border-radius: 5px;
  }

  /* Button styling */
  #myBtn {
    margin-top: 10px;
    cursor: pointer;
  }
</style>

<script>
function filterProducts() {
  var searchFilter = document.getElementById('search').value.toLowerCase();
  var rows = document.querySelectorAll('.product-row');
  
  rows.forEach(row => {
    var productName = row.cells[0].innerText.toLowerCase();
    var categoryName = row.cells[1].innerText.toLowerCase();
    var shouldDisplay = productName.includes(searchFilter) || categoryName.includes(searchFilter);

    // Get the product ID from the onclick attribute
    var onclick = row.cells[0].getAttribute('onclick');
    var idMatch = onclick.match(/toggleStock\((\d+)\)/);
    var id = idMatch ? idMatch[1] : null;

    row.style.display = shouldDisplay ? '' : 'none';

    // Also hide the corresponding stock details row
    if (id) {
      var stockRow = document.getElementById('stock-' + id);
      if (stockRow) {
        stockRow.style.display = 'none';
      }
    }
  });
}

function openReportModal(stockId) {
  document.getElementById("stockIdReport").value = stockId;
  var reportModal = new bootstrap.Modal(document.getElementById("reportModal"));
  reportModal.show();
}

function toggleStock(productId) {
  var stockRow = document.getElementById('stock-' + productId);
  if (stockRow) {
    stockRow.style.display = stockRow.style.display === 'none' ? 'table-row' : 'none';
  }
}

function confirmFound(stockId) {
  if (confirm("Are you sure this item has been found and is now available again?")) {
    window.location.href = "found_item.php?stock_id=" + stockId + "&status=1";
  }
}

// Form submission handler
document.addEventListener('DOMContentLoaded', function() {
  var reportForm = document.getElementById('reportForm');
  if (reportForm) {
    reportForm.addEventListener('submit', function(e) {
      var statusSelect = document.getElementById('status');
      if (!statusSelect.value) {
        e.preventDefault();
        alert('Please select a status');
        return false;
      }
      // Form will submit normally if validation passes
    });
  }
});
</script>

<?php include_once('layouts/footer.php'); ?>