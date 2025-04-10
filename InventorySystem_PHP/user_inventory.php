<?php
$page_title = 'Inventory by Location';
require_once('includes/load.php');
page_require_level(3);

// Get the logged-in user's location_id
$user = current_user(); // Make sure this function is correctly defined in `load.php`

// Ensure $user is properly set before accessing properties
$user_location = isset($user['location_id']) ? $user['location_id'] : '';

$all_locations = find_all('location');
$all_categories = find_all('categorie');

// Fetch categories safely
$categories = $db->query("SELECT * FROM categories");

if (!$categories) {
    die("Query failed: " . $db->error);
}

$categories = $categories->fetch_all(MYSQLI_ASSOC); // Fetch results as an associative array

// Check if 'location' exists in 'stock' table before querying
$location_check = $db->query("SHOW COLUMNS FROM stock LIKE 'location_id'");

if ($location_check->num_rows > 0) {
    $locations = $db->query("SELECT DISTINCT location_id FROM stock");

    if (!$locations) {
        die("Query failed: " . $db->error);
    }

    $locations = $locations->fetch_all(MYSQLI_ASSOC);
} else {
    $locations = []; // If 'location' column doesn't exist, return an empty array
}

// Fetch products based on the user's location - only products with stock > 0
$products = [];
if (!empty($user_location)) {
    $sql = "SELECT products.id, 
                   products.name AS product_name,
                   COUNT(stock.id) AS stock_count, 
                   categories.name AS categorie
            FROM products
            JOIN stock ON products.id = stock.product_id AND stock.location_id = '{$db->escape($user_location)}'
            JOIN categories ON products.categorie_id = categories.id
            GROUP BY products.id
            HAVING stock_count > 0";

    $result = $db->query($sql);
    $products = $db->while_loop($result);
}
?>

<?php include_once('layouts/header.php'); ?>
<h2>Inventory in <?php echo remove_junk(ucfirst($user_location)); ?> </h2>

<input type="text" id="search" placeholder="Search" onkeyup="filterProducts()">

<?php
// Display success or error messages
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status = $_GET['status'];
    $message = $_GET['message'];

    if ($status == 'success') {
        echo "<div style='color: green; text-align: center;'>$message</div>";
    } else if ($status == 'error') {
        echo "<div style='color: red; text-align: center;'>$message</div>";
    }
}

// Display a message if no products are found
if (empty($products)) {
    echo "<div style='text-align: center; margin: 20px;'>No products with inventory found at this location.</div>";
}
?>

<?php if (!empty($products)): ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td onclick="toggleStock(<?php echo $product['id']; ?>)" style="cursor:pointer;">
                <?php echo htmlspecialchars($product['product_name']); ?>
            </td>
            <td><?php echo htmlspecialchars($product['categorie']); ?></td>
            <td><?php echo htmlspecialchars($product['stock_count']); ?></td>
        </tr>

        <!-- Stock List -->
        <tr id="stock-<?php echo $product['id']; ?>" style="display:none;">
            <td colspan="3">
                <table class="table table-bordered">
                    <tr>
                        <th>Stock ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php 
                        $stockQuery = "
                        SELECT stock.id, stock.stock_number, 
                               status.name AS status_name, 
                               location.name AS location_name
                        FROM stock
                        LEFT JOIN status ON stock.status_id = status.id
                        LEFT JOIN location ON stock.location_id = location.id
                        WHERE stock.product_id = " . $product['id'] . "
                        AND stock.location_id = '{$db->escape($user_location)}'";

                        $stockResult = $db->query($stockQuery);
                        
                        if ($stockResult && $stockResult->num_rows > 0) {
                            while ($stock = $stockResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($stock['stock_number']); ?></td>
                                    <td><?php echo htmlspecialchars($stock['status_name']); ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" onclick="openReportModal(<?php echo $stock['id']; ?>)">Report Missing/Lost</button>
                                        <?php if ($stock['status_name'] == 'Missing') { ?>
                                            <span class="btn btn-success btn-sm" onclick="confirmFound(<?php echo $stock['id']; ?>)">Found</span>
                                        <?php } ?>
                                        <?php if ($stock['status_name'] == 'Lost') { ?>
                                            <span class="btn btn-primary btn-sm" onclick="">Request</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="3">No stock items found at this location.</td>
                            </tr>
                        <?php } ?>
                </table>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- Report Missing/Lost Modal -->
<!-- Report Missing/Lost Modal -->
<div id="reportModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeReportModal()">&times;</span>
    <h3>Report Item as Missing or Lost</h3>
    <form id="reportForm" action="report_missing.php" method="POST">
      <input type="hidden" id="stockIdReport" name="stock_id">
      <label for="status">Choose Status:</label>
      <select id="status" name="status" class="form-control">
        <option value="">Select Status</option>
        <option value="3">Missing</option>
        <option value="4">Lost</option>
      </select>
      <br>
      <button type="submit" class="btn btn-danger">Submit Report</button>
      
    </form>

    <!-- Message and Available Again Button -->
    <div id="reportMessage" style="margin-top: 15px; font-weight: bold; color: green;"></div>
    <button id="availableAgainBtn" class="btn btn-success" style="display:none;" onclick="markAsAvailable(document.getElementById('stockIdReport').value)">Report as Available Again</button>
  </div>
</div>

<style>
  .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    padding-top: 60px;
  }

  .modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 400px;
    border-radius: 10px;
  }

  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
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
    var rows = document.querySelectorAll('tbody > tr:nth-child(odd)'); // Get only main product rows
    
    rows.forEach(row => {
        var productName = row.cells[0].innerText.toLowerCase();
        var categoryName = row.cells[1].innerText.toLowerCase();
        var shouldDisplay = productName.includes(searchFilter) || categoryName.includes(searchFilter);
        
        // Get the ID from the onclick attribute
        var onclick = row.cells[0].getAttribute('onclick');
        var idMatch = onclick.match(/toggleStock\((\d+)\)/);
        var id = idMatch ? idMatch[1] : null;
        
        // Hide/show the main row
        row.style.display = shouldDisplay ? '' : 'none';
        
        // Hide the stock row if it exists
        if (id) {
            var stockRow = document.getElementById('stock-' + id);
            if (stockRow) {
                stockRow.style.display = 'none'; // Always hide when filtering
            }
        }
    });
}

function openReportModal(stockId) {
    document.getElementById("stockIdReport").value = stockId;
    document.getElementById("reportModal").style.display = "block";
}

function closeReportModal() {
    document.getElementById("reportModal").style.display = "none";
}

window.onclick = function(event) {
    var modal = document.getElementById("reportModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function toggleStock(productId) {
    var stockRow = document.getElementById('stock-' + productId);
    stockRow.style.display = stockRow.style.display === 'none' ? 'table-row' : 'none';
}


// Bind the submit function to the form
document.getElementById('reportForm').addEventListener('submit', submitReportForm);

function confirmFound(stockId) {
    // Display confirmation dialog
    var confirmAction = confirm("Are you sure this item has been found and is now available again?");

    if (confirmAction) {
        // Send a request to PHP to update the item's status to available
        window.location.href = "found_item.php?stock_id=" + stockId + "&status=1";
    }
}


</script>

<?php include_once('layouts/footer.php'); ?>
