<?php
$page_title = 'All Product';
require_once('includes/load.php');
page_require_level(2);

$all_location = find_all('location');
$all_categories = find_all('categorie');
// Fetch categories safely
$categories = $db->query("SELECT * FROM categories");

if (!$categories) {
    die("Query failed: " . $db->error);
}

$categories = $categories->fetch_all(MYSQLI_ASSOC); // Fetch results as an associative array

// Check if 'location' exists in 'stock' table before querying
$location_check = $db->query("SHOW COLUMNS FROM stock LIKE 'location'");

if ($location_check->num_rows > 0) {
    $locations = $db->query("SELECT DISTINCT location FROM stock");

    if (!$locations) {
        die("Query failed: " . $db->error);
    }

    $locations = $locations->fetch_all(MYSQLI_ASSOC);
} else {
    $locations = []; // If 'location' column doesn't exist, return an empty array
}

$products = join_product_table();
?>

<?php include_once('layouts/header.php'); ?>
<h2>All Inventory</h2>

  <input type="text" id="search" placeholder="Search" onkeyup="filterProducts()">

  <button onclick="window.location.href='add_product.php'">Add New</button>

<?php
// Check if the 'status' and 'message' query parameters are set
if (isset($_GET['status']) && isset($_GET['message'])) {
  $status = $_GET['status'];
  $message = $_GET['message'];

  // Display the message based on the status
  if ($status == 'success') {
      echo "<div style='color: green; text-align: center;'>$message</div>";
  } else if ($status == 'error') {
      echo "<div style='color: red; text-align: center;'>$message</div>";
  }
}
?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
          <?php foreach ($products as $product): ?>
          <tr>
              <td onclick="toggleStock(<?php echo $product['id']; ?>)" style="cursor:pointer;">
                  <?php echo htmlspecialchars($product['name']); ?>
              </td>
              <td><?php echo htmlspecialchars($product['categorie']); ?></td>
              <td><?php echo htmlspecialchars($product['quantity']); ?></td>
              
              <td>
                <a href="edit_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-info btn-sm" title="Edit" data-toggle="tooltip">
                  Edit
                </a>
                <a href="delete_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-danger btn-sm" title="Delete" data-toggle="tooltip" onclick="return confirmDelete();">
                  Delete
                </a>
              </td>
          </tr>
          
          <!-- Stock List -->
          <tr id="stock-<?php echo $product['id']; ?>" style="display:none;">
              <td colspan="4">
                  <table class="table table-bordered">
                      <tr>
                          <th>Stock ID</th>
                          <th>Location</th>
                          <th>Status</th>     
                          <th>Action</th>
                      </tr>
                      <?php 
                          $stockQuery = "
                          SELECT stock.*, 
                                status.name AS status_name, 
                                location.name AS location_name
                          FROM stock
                          LEFT JOIN status ON stock.status_id = status.id
                          LEFT JOIN location ON stock.location_id = location.id
                          WHERE stock.product_id = " . $product['id'];

                          $stockResult = $db->query($stockQuery);

                          while ($stock = mysqli_fetch_assoc($stockResult)) { ?>
                          <tr>
                              <td><?php echo htmlspecialchars($stock['stock_number']); ?></td>
                              <td><?php echo htmlspecialchars($stock['location_name']); ?></td> <!-- Display location name -->
                              <td><?php echo htmlspecialchars($stock['status_name']); ?></td> <!-- Display status name -->
                              <td>
                                  <button class="btn btn-warning btn-sm" onclick="openLocationModal(<?php echo $stock['id']; ?>)">Change Location</button>
                                  <button class="btn btn-secondary btn-sm" onclick="openStatusModal(<?php echo $stock['id']; ?>)">Change Status</button>
                                  <?php if ($stock['status_name'] == 'Available') { ?>
                                    <span class="text-primary">Borrowable</span>
                                  <?php } elseif  ($stock['status_name'] == 'Placed') { ?>
                                    <span class="text-secondary">In Place</span>
                                  <?php } else { ?>
                                      <span class="text-danger">Unavailable</span>
                                  <?php } ?>
                              </td>
                          </tr>
                          <?php } ?>

                  </table>
              </td>
          </tr>
      <?php endforeach; ?>
    </tbody>
</table>

<div id="locationModal" class="modal">
  <!-- Modal Content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Select Location</h3>
    <form id="locationForm" action="update_stock_location.php" method="POST">
      <input type="hidden" id="stockIdLocation" name="stock_id">
      <label for="location">Choose a Location:</label>
      <select id="location" name="location" class="form-control">
        <option value=""> Select a location</option>
        <?php foreach ($all_location as $loc): ?>
            <option value="<?php echo (int)$loc['id']; ?>"  
            <?php 
                if (isset($stock['location_id']) && $stock['location_id'] === (int)$loc['id']) {
                    echo "selected";
                }
            ?>>
              <?php echo remove_junk($loc['name']); ?>
            </option>
        <?php endforeach; ?>
      </select>
      <br>
      <button type="submit" class="btn btn-primary">Update Location</button>
    </form>
  </div>
</div>
<!-- Status Change Modal -->
<div id="statusModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Select Status</h3>
    <form id="statusForm" action="update_stock_status.php" method="POST">
      <input type="hidden" id="stockIdStatus" name="stock_id">
      <label for="status">Choose a Status:</label>
      <select id="status" name="status" class="form-control">
        <option value=""> Select a status</option>
        <option value="1">Available</option>
        <option value="2">Borrowed</option>
        <option value="3">Missing</option>
        <option value="4">Lost</option>
        <option value="5">Maintenance</option>
        <option value="6">Placed</option>
      </select>
      <br>
      <button type="submit" class="btn btn-primary">Update Status</button>
    </form>
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
    function toggleStock(productId) {
        var stockRow = document.getElementById('stock-' + productId);
        stockRow.style.display = stockRow.style.display === 'none' ? 'table-row' : 'none';
    }
    
    function filterProducts() {
    var searchFilter = document.getElementById('search').value.toLowerCase();

    // Select all product rows (excluding the stock rows)
    var rows = document.querySelectorAll('tbody tr:not([id^=stock])');
    
    rows.forEach(row => {
        var productName = row.cells[0].innerText.toLowerCase();
        var matchesSearch = productName.includes(searchFilter);

        // Display row if it matches all filters
        if (matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}


    function confirmDelete() {
        return confirm("Are you sure you want to delete this product? This action cannot be undone.");
    }

function openStatusModal(stockId) {
    document.getElementById("stockIdStatus").value = stockId;
    modalStatus.style.display = "block";
}

function openLocationModal(stockId) {
    document.getElementById("stockIdLocation").value = stockId;
    modalLocation.style.display = "block";
}

// Get the modals for status and location
var modalStatus = document.getElementById("statusModal");
var modalLocation = document.getElementById("locationModal");

// Get the <span> elements that close the modals
var spanStatus = document.getElementsByClassName("close")[1]; // Assuming it's the second close button for status
var spanLocation = document.getElementsByClassName("close")[0]; // Assuming it's the first close button for location

// When the user clicks on <span> (x), close the corresponding modal
spanStatus.onclick = function() {
    modalStatus.style.display = "none";
}

spanLocation.onclick = function() {
    modalLocation.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close the corresponding modal
window.onclick = function(event) {
    if (event.target == modalStatus) {
        modalStatus.style.display = "none";
    } else if (event.target == modalLocation) {
        modalLocation.style.display = "none";
    }
}


</script>
<?php include_once('layouts/footer.php'); ?>
