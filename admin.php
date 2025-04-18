<?php
  $page_title = 'Admin Dashboard';
  require_once('includes/load.php');
  page_require_level(1);
?>
<?php include_once('layouts/header.php'); ?>
<?php $stockData = getStockBreakdown(); ?>

<!-- Dashboard Welcome Section with Background -->
<div class="py-5 px-4 mb-5 rounded-4 shadow-sm" style="background: linear-gradient(to bottom,rgb(220, 244, 255), #ffffff);">
  <div class="text-center mb-4">
    <h1 class="fw-bold text-primary">System Dashboard</h1>
  </div>

  <!-- Summary Cards -->
  <div class="row g-4">
    <!-- Missing/Lost Items -->
    <div class="col-md-4">
      <div class="card text-white bg-success shadow-sm h-100" data-bs-toggle="modal" data-bs-target="#missingLostModal" style="cursor: pointer;">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-eye-slash display-4 me-3"></i>
          <div>
            <h3 class="mb-0"><?php echo count_missinglost_items(); ?></h3>
            <p class="mb-0">Missing / Lost Items</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Actions -->
    <div class="col-md-4">
      <div class="card text-white bg-primary shadow-sm h-100" data-bs-toggle="modal" data-bs-target="#recentActionsModal" style="cursor: pointer;">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-arrow-repeat display-4 me-3"></i>
          <div>
            <h3 class="mb-0">3</h3>
            <p class="mb-0">Action Logs</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Low Stock -->
    <div class="col-md-4">
      <div class="card text-white bg-danger shadow-sm h-100" data-bs-toggle="modal" data-bs-target="#lowStockModal" style="cursor: pointer;">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-exclamation-triangle display-4 me-3"></i>
          <div>
            <h3 class="mb-0">1</h3>
            <p class="mb-0">Low Stock Items</p>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- Quick Scan Button -->
    <div class="d-flex justify-content-center mt-5">
        <button class="btn btn-lg btn-primary shadow-lg rounded-pill d-flex align-items-center justify-content-center px-4 py-2" id="scanBtn">
            <i class="bi bi-upc-scan me-2"></i> <!-- Barcode icon -->
            <span>Scan Barcode</span>
        </button>
    </div>

</div>


<!-- Updated Barcode Modal - Grocery Style (Inventory Focus) -->
<div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="barcodeModalLabel">Product Scanner</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Hidden input for barcode scanning -->
        <input type="text" id="hiddenBarcodeInput" style="position: absolute; left: -9999px;" autofocus>
        
        <!-- Scanning status and instructions -->
        <div id="scanningStatus" class="alert alert-info d-flex align-items-center mb-3">
          <i class="bi bi-upc-scan me-2 fs-4"></i>
          <div id="barcodeFeedback">Scan a product barcode to begin</div>
        </div>
        
        <!-- Scanned Items Table -->
        <div class="table-responsive mb-3">
          <table class="table table-hover" id="scannedItemsTable">
            <thead class="table-light">
              <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">Product</th>
                <th style="width: 20%">Quantity</th>
                <th style="width: 10%">Available</th>
                <th style="width: 10%">Actions</th>
              </tr>
            </thead>
            <tbody id="scannedItemsList">
              <!-- Scanned items will be added here dynamically -->
              <tr id="noItemsRow">
                <td colspan="5" class="text-center py-4 text-muted">No items scanned yet</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="table-light fw-bold">
                <td colspan="3" class="text-end">Total Items:</td>
                <td id="totalQty">0</td>
                <td colspan="2"></td>
              </tr>
            </tfoot>
          </table>
        </div>
        
        <!-- Quick Quantity Panel -->
        <div id="quickQtyPanel" class="card mb-3" style="display: none;">
          <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0" id="quickQtyProduct">Select Quantity</h5>
              <button type="button" class="btn-close" id="closeQuickQty"></button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="input-group">
                  <span class="input-group-text">Quantity</span>
                  <input type="number" class="form-control" id="quickQtyInput" value="1" min="1">
                  <button class="btn btn-outline-secondary" type="button" id="decQty">-</button>
                  <button class="btn btn-outline-secondary" type="button" id="incQty">+</button>
                </div>
                <small class="text-muted">Available: <span id="quickQtyAvailable">0</span></small>
              </div>
              <div class="col-md-6 d-flex align-items-center justify-content-end">
                <button class="btn btn-primary" id="confirmQuickQty">Confirm</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <div>
          <button type="button" class="btn btn-outline-danger" id="clearAllBtn">
            <i class="bi bi-trash me-1"></i> Clear All
          </button>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </button>
        </div>
        <button id="checkoutBtn" class="btn btn-success" disabled>
          <i class="bi bi-check-circle me-1"></i> Confirm Usage
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notifications -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="scannerToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="me-auto" id="toastTitle">Product Scanner</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="toastMessage"></div>
  </div>
</div>
                
<!-- Recent Actions Modal -->
<div class="modal fade" id="recentActionsModal" tabindex="-1" aria-labelledby="recentActionsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-semibold" id="recentActionsModalLabel">
          <i class="bi bi-clock-history me-2"></i> Recent Admin Actions
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Placeholder for recent actions content -->
        <ul class="list-group list-group-flush">
            <?php
                $recent_actions = find_recent_actions(10); // Fetch last 10 actions
                if (!empty($recent_actions)):
                    foreach ($recent_actions as $action):
            ?>
                <li class="list-group-item">
                <strong><?php echo remove_junk($action['action']); ?></strong>
                <br>
                <small class="text-muted">
                    By: <?php echo remove_junk($action['username']); ?> | 
                    <?php echo date("F j, Y, g:i a", strtotime($action['timestamp'])); ?>
                </small>

                </li>
            <?php 
                    endforeach;
                else: 
            ?>
                <li class="list-group-item text-muted">No recent actions</li>
            <?php endif; ?>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Missing/Lost Items Modal -->
<div class="modal fade" id="missingLostModal" tabindex="-1" aria-labelledby="missingLostModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header bg-success text-white rounded-top-4">
        <h5 class="modal-title fw-semibold" id="missingLostModalLabel">
          <i class="bi bi-eye-slash me-2"></i> Missing / Lost Items
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Placeholder for missing/lost item content -->
        <div class="alert alert-info">There are no missing or lost items currently.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Low Stock Items Modal -->
<div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header bg-danger text-white rounded-top-4">
        <h5 class="modal-title fw-semibold" id="lowStockModalLabel">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Low Stock Items
        </h5>
        <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Placeholder for low stock item list -->
        <div class="alert alert-info">
          All items are sufficiently stocked.
        </div>
        <!-- You can replace the alert with a table if items exist -->
        <!--
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Category</th>
              <th>Quantity</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Example Item</td>
              <td>Consumables</td>
              <td><span class="badge bg-danger">3</span></td>
            </tr>
          </tbody>
        </table>
        -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Control Panel (Quick Actions) -->
<div class="d-flex justify-content-center mt-4">
    <a href="add_product.php" class="btn btn-lg shadow-lg rounded-pill d-flex align-items-center justify-content-center px-4 py-2" id="addItemBtn">
        <i class="bi bi-plus-circle me-2"></i> <!-- Add Item icon -->
        <span>Add Item</span>
    </a>
    <a href="generate_report.php" class="btn btn-lg mx-2 shadow-lg rounded-pill d-flex align-items-center justify-content-center px-4 py-2" id="generateReportBtn">
        <i class="bi bi-file-earmark-bar-graph me-2"></i> <!-- Generate Report icon -->
        <span>Generate Report</span>
    </a>
    <a href="users.php" class="btn btn-lg mx-2 shadow-lg rounded-pill d-flex align-items-center justify-content-center px-4 py-2" id="manageUsersBtn">
        <i class="bi bi-person-fill me-2"></i> <!-- Manage Users icon -->
        <span>Manage Users</span>
    </a>
</div>




<!-- Control Section -->
  <!-- Logged In Users and Requests -->
  <div class="row mt-5">
    <div class="col-md-6">
      <div class="card shadow-lg">
        <div class="card-header bg-secondary text-white">
          <i class="bi bi-person"></i> Currently Logged In Users
        </div>
        <div class="card-body">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Last Login</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $logged_in_users = fetch_logged_in_users();
              if (!empty($logged_in_users)) {
                foreach ($logged_in_users as $user) {
                  echo "<tr>";
                  echo "<td>" . remove_junk($user['username']) . "</td>";
                  echo "<td>" . remove_junk($user['user_level']) . "</td>";
                  echo "<td>" . date("F j, Y, g:i a", strtotime($user['last_login'])) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='3' class='text-center'>No users currently logged in</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-lg">
        <div class="card-header bg-secondary text-white">
          <i class="bi bi-send"></i> User Item Requests
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>User</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Requested At</th>
              </tr>
            </thead>
            <tbody>
              <!-- Dynamic request rows (unchanged PHP) -->
              <?php
                $requests = $db->query("SELECT r.*, u.name AS user_name, r.categorie_id FROM item_requests r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.request_date DESC");
                while ($req = $requests->fetch_assoc()):
              ?>
              <tr>
                <td><?php echo remove_junk(ucfirst($req['user_name'])); ?></td>
                <td><?php echo remove_junk(ucfirst($req['item_name'])); ?></td>
                <td><?php echo (int)$req['quantity']; ?></td>
                <td><?php echo $req['status']; ?></td>
                <td><?php echo read_date($req['request_date']); ?></td>
                <td>
                  <?php if ($req['status'] == 'Pending'): ?>
                    <button class="btn btn-success btn-sm" onclick="confirmAction('approve', <?php echo $req['id']; ?>)">Approve</button>
                    <button class="btn btn-danger btn-sm" onclick="confirmAction('deny', <?php echo $req['id']; ?>)">Deny</button>
                  <?php elseif ($req['status'] === 'Approved'): ?>
                    <form method="POST" action="request_available.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to make this item available?');">
                      <input type="hidden" name="request_id" value="<?= $req['id']; ?>">
                      <input type="hidden" name="product-title" value="<?= remove_junk($req['item_name']); ?>">
                      <input type="hidden" name="product-categorie" value="<?= (int)$req['categorie_id']; ?>">
                      <input type="hidden" name="product-quantity" value="<?= (int)$req['quantity']; ?>">
                      <input type="hidden" name="product-location" value="">
                      <input type="hidden" name="product-photo" value="0">
                      <button type="submit" name="make_available" class="btn btn-primary btn-sm">Make Available</button>
                    </form>
                  <?php elseif ($req['status'] == 'Added'): ?>
                    <span class="badge bg-success">Item Added</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Inventory Insights (Charts) -->
<div class="row" style="margin-top: 2rem;">
    <div class="col-md-6" style="margin-bottom: 1rem;">
        <!-- Inventory Trends (Consumed Items) -->
        <div class="card shadow-lg" style="border-radius: 0;">
            <div class="card-header bg-secondary text-white">
            <i class="bi bi-graph-up"></i> Inventory Trends (Consumed Items)
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <canvas id="lineChart" style="max-width: 100%; height: auto;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6" style="margin-bottom: 1rem;">
        <!-- Category Breakdown -->
        <div class="card shadow-lg" style="border-radius: 0;">
            <div class="card-header bg-secondary text-white">
            <i class="bi bi-pie-chart"></i> Category Breakdown
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <canvas id="pieChart" style="max-width: 100%; height: auto;"></canvas>
            </div>
        </div>
    </div>
</div>



<!-- Chart.js for Graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Line Chart (Consumed Items)
    document.addEventListener("DOMContentLoaded", function () {
        fetch("get_chart_data.php")
            .then(response => response.json())
            .then(data => {
                var ctx1 = document.getElementById("lineChart").getContext("2d");
                var lineChart = new Chart(ctx1, {
                    type: "line",
                    data: {
                        labels: data.months,
                        datasets: [{
                            label: "Consumed",
                            data: data.consumed,  // Use "consumed" data instead of "borrowed"
                            borderColor: "blue",
                            fill: false
                        }]
                    }
                });
            })
            .catch(error => console.error("Error loading chart data:", error));
    });

    // Pie Chart (Stock Category Breakdown)
    var ctx2 = document.getElementById("pieChart").getContext("2d");

    // Get stock breakdown data from PHP
    var stockCategories = <?php echo json_encode($stockData); ?>;

    // Prepare data for the Pie Chart
    var labels = [];
    var data = [];
    
    // Generate dynamic colors for each category
    var backgroundColor = [];
    var hue = 0; // Starting Hue value

    stockCategories.forEach(function(item, index) {
        labels.push(item.category_name); // Category name
        data.push(item.total_stock); // Total stock for the category

        // Generate a color using HSL (with varying hue and constant saturation/lightness)
        var color = `hsl(${hue}, 70%, 60%)`; // HSL color (hue, saturation, lightness)
        backgroundColor.push(color);
        
        // Increment the hue to get a new color for the next category
        hue += 360 / stockCategories.length; // Adjust hue based on the number of categories
        if (hue > 360) hue = 0; // Reset hue after a full circle
    });

    var pieChart = new Chart(ctx2, {
        type: "pie",
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColor
            }]
        }
    });



    document.addEventListener("DOMContentLoaded", () => {
    // Elements
    const scanBtn = document.getElementById("scanBtn");
    const barcodeModal = new bootstrap.Modal(document.getElementById("barcodeModal"));
    const hiddenInput = document.getElementById("hiddenBarcodeInput");
    const barcodeFeedback = document.getElementById("barcodeFeedback");
    const scanningStatus = document.getElementById("scanningStatus");
    const scannedItemsList = document.getElementById("scannedItemsList");
    const noItemsRow = document.getElementById("noItemsRow");
    const checkoutBtn = document.getElementById("checkoutBtn");
    const clearAllBtn = document.getElementById("clearAllBtn");
    const totalQtyEl = document.getElementById("totalQty");
    
    // Quick quantity panel elements
    const quickQtyPanel = document.getElementById("quickQtyPanel");
    const quickQtyProduct = document.getElementById("quickQtyProduct");
    const quickQtyInput = document.getElementById("quickQtyInput");
    const quickQtyAvailable = document.getElementById("quickQtyAvailable");
    const incQtyBtn = document.getElementById("incQty");
    const decQtyBtn = document.getElementById("decQty");
    const confirmQuickQtyBtn = document.getElementById("confirmQuickQty");
    const closeQuickQtyBtn = document.getElementById("closeQuickQty");
    
    // Toast elements
    const scannerToastEl = document.getElementById("scannerToast");
    const scannerToast = scannerToastEl ? new bootstrap.Toast(scannerToastEl) : null;
    const toastTitle = document.getElementById("toastTitle");
    const toastMessage = document.getElementById("toastMessage");

    // Check if it's initialized
    if (!scannerToast) {
        console.error("Failed to initialize toast");
    }
    
    // Data structures
    let scannedItems = [];
    let currentEditItemId = null;
    
    // Initialize
    scanBtn.addEventListener("click", () => {
        resetScannerUI();
        barcodeModal.show();
        setTimeout(() => hiddenInput.focus(), 500);
    });
    
    // Focus management
    document.getElementById('barcodeModal').addEventListener('shown.bs.modal', () => {
        hiddenInput.value = "";
        hiddenInput.focus();
    });
    
    document.getElementById('barcodeModal').addEventListener('click', () => {
        if (!document.activeElement.matches('input[type="number"], button')) {
            setTimeout(() => hiddenInput.focus(), 100);
        }
    });
    
    // Handle barcode input
    hiddenInput.addEventListener("input", function() {
        if (this.value.includes('\n') || this.value.includes('\r')) {
            processBarcode(this.value.trim().replace(/[\r\n]/g, ''));
            this.value = "";
        }
    });
    
    hiddenInput.addEventListener("keydown", function(e) {
        if (e.key === "Enter") {
            processBarcode(this.value.trim());
            this.value = "";
            e.preventDefault();
        }
    });
    
    // Process barcode
    function processBarcode(barcode) {
        if (!barcode) return;
        
        updateScanFeedback("Searching...", "info");
        
        fetch(`get_product_by_barcode.php?barcode=${encodeURIComponent(barcode)}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! Status: ${res.status}`);
                }
                // Debug the raw response text
                return res.text().then(text => {
                    console.log("Raw API response:", text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error("JSON Parse Error:", e);
                        console.error("Problematic text:", text);
                        throw new Error("Invalid JSON response");
                    }
                });
            })
            .then(data => {
                if (data.error) {
                    updateScanFeedback(`Product not found: ${barcode}`, "error");
                    showToast("Error", `No product found with barcode: ${barcode}`, "danger");
                } else {
                    // Check if product is already in list
                    const existingItemIndex = scannedItems.findIndex(item => item.id === data.id);
                    
                    if (existingItemIndex !== -1) {
                        // Increment quantity if stock allows
                        if (scannedItems[existingItemIndex].quantity < scannedItems[existingItemIndex].available) {
                            scannedItems[existingItemIndex].quantity += 1;
                            updateItemRow(existingItemIndex);
                            highlightRow(scannedItems[existingItemIndex].id);
                        } else {
                            showToast("Maximum Quantity", "Cannot add more - stock limit reached", "warning");
                        }
                    } else {
                        // Add new item
                        const newItem = {
                            id: data.id,
                            name: data.name,
                            quantity: 1,
                            available: parseInt(data.quantity || 0),
                            barcode: barcode
                        };
                        
                        scannedItems.push(newItem);
                        addItemToTable(newItem, scannedItems.length - 1);
                    }
                    
                    updateScanFeedback(`Added: ${data.name}`, "success");
                    showToast("Product Added", data.name, "success");
                    updateTotals();
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                updateScanFeedback("Error fetching product information", "error");
            })
            .finally(() => {
                // Re-focus the hidden input
                setTimeout(() => hiddenInput.focus(), 100);
            });
    }
    
    // Update scan feedback UI
    function updateScanFeedback(message, type) {
        barcodeFeedback.textContent = message;
        
        // Reset classes
        scanningStatus.classList.remove("alert-info", "alert-danger", "alert-success", "scanning-error", "scanning-success");
        
        // Apply appropriate styling
        switch(type) {
            case "error":
                scanningStatus.classList.add("alert-danger", "scanning-error");
                break;
            case "success":
                scanningStatus.classList.add("alert-success", "scanning-success");
                break;
            default:
                scanningStatus.classList.add("alert-info");
        }
        
        // Auto-reset to ready state after success/error
        if (type === "success" || type === "error") {
            setTimeout(() => {
                scanningStatus.classList.remove("alert-danger", "alert-success", "scanning-error", "scanning-success");
                scanningStatus.classList.add("alert-info");
                barcodeFeedback.textContent = "Ready to scan next item";
            }, 3000);
        }
    }
    
    // Add item to the table
    function addItemToTable(item, index) {
        // Hide the "no items" row if it's visible
        if (noItemsRow) {
            noItemsRow.style.display = 'none';
        }
        
        const row = document.createElement('tr');
        row.id = `item-row-${item.id}`;
        row.dataset.index = index;
        
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.name}</td>
            <td>
                <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary decrement-btn" type="button" data-id="${item.id}">&minus;</button>
                    <input type="number" class="form-control text-center item-qty" value="${item.quantity}" min="1" max="${item.available}" data-id="${item.id}">
                    <button class="btn btn-outline-secondary increment-btn" type="button" data-id="${item.id}">&plus;</button>
                </div>
            </td>
            <td class="text-center">${item.available}</td>
            <td>
                <button class="btn btn-sm btn-outline-danger remove-item" data-id="${item.id}"><i class="bi bi-trash"></i></button>
            </td>
        `;
        
        scannedItemsList.appendChild(row);
        
        // Add event listeners to the new row
        attachRowEventListeners(row);
        
        // Enable checkout button if we have items
        checkoutBtn.disabled = false;
        
        // Highlight the new row
        highlightRow(item.id);
    }
    
    // Update an existing row
    function updateItemRow(index) {
        const item = scannedItems[index];
        const row = document.getElementById(`item-row-${item.id}`);
        
        if (row) {
            const qtyInput = row.querySelector('.item-qty');
            qtyInput.value = item.quantity;
        }
    }
    
    // Attach event listeners to row elements
    function attachRowEventListeners(row) {
        // Quantity input
        const qtyInput = row.querySelector('.item-qty');
        qtyInput.addEventListener('change', function() {
            const id = this.dataset.id;
            const index = scannedItems.findIndex(item => item.id == id);
            let newQty = parseInt(this.value);
            
            if (isNaN(newQty) || newQty < 1) {
                newQty = 1;
            } else if (newQty > scannedItems[index].available) {
                newQty = scannedItems[index].available;
                showToast("Maximum Quantity", "Cannot exceed available stock", "warning");
            }
            
            scannedItems[index].quantity = newQty;
            this.value = newQty;
            updateTotals();
        });
        
        // Increment button
        const incrementBtn = row.querySelector('.increment-btn');
        incrementBtn.addEventListener('click', function() {
            const id = this.dataset.id;
            const index = scannedItems.findIndex(item => item.id == id);
            
            if (scannedItems[index].quantity < scannedItems[index].available) {
                scannedItems[index].quantity++;
                updateItemRow(index);
                updateTotals();
            } else {
                showToast("Maximum Quantity", "Cannot exceed available stock", "warning");
            }
        });
        
        // Decrement button
        const decrementBtn = row.querySelector('.decrement-btn');
        decrementBtn.addEventListener('click', function() {
            const id = this.dataset.id;
            const index = scannedItems.findIndex(item => item.id == id);
            
            if (scannedItems[index].quantity > 1) {
                scannedItems[index].quantity--;
                updateItemRow(index);
                updateTotals();
            }
        });
        
        // Remove button
        const removeBtn = row.querySelector('.remove-item');
        removeBtn.addEventListener('click', function() {
            const id = this.dataset.id;
            removeItem(id);
        });
    }
    
    // Remove an item
    function removeItem(id) {
        const index = scannedItems.findIndex(item => item.id == id);
        if (index !== -1) {
            // Remove from array
            const removedItem = scannedItems.splice(index, 1)[0];
            
            // Remove from table
            const row = document.getElementById(`item-row-${id}`);
            if (row) {
                row.remove();
            }
            
            // Show "no items" row if no items left
            if (scannedItems.length === 0) {
                noItemsRow.style.display = '';
                checkoutBtn.disabled = true;
            } else {
                // Renumber remaining rows
                document.querySelectorAll('#scannedItemsList tr:not(#noItemsRow)').forEach((row, idx) => {
                    row.cells[0].textContent = idx + 1;
                });
            }
            
            updateTotals();
            showToast("Item Removed", `Removed: ${removedItem.name}`, "info");
        }
    }
    
    // Update totals
    function updateTotals() {
        const totalQty = scannedItems.reduce((sum, item) => sum + item.quantity, 0);
        totalQtyEl.textContent = totalQty;
    }
    
    // Highlight a newly scanned row
    function highlightRow(id) {
        const row = document.getElementById(`item-row-${id}`);
        if (row) {
            // Remove any existing highlights
            document.querySelectorAll('#scannedItemsList tr.last-scanned').forEach(r => {
                r.classList.remove('last-scanned');
            });
            
            // Add highlight
            row.classList.add('last-scanned');
        }
    }
    
    function showToast(title, message, type = "info") {
    // Make sure elements exist before using them
    if (!toastTitle || !toastMessage || !scannerToast) {
        console.error("Toast elements not properly initialized");
        return;
    }
    
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    // Get the actual DOM element of the toast if it's a Bootstrap object
    const toastElement = scannerToast._element || document.getElementById("scannerToast");
    
    // Check if the element has classList before using it
    if (toastElement && toastElement.classList) {
        // Remove existing color classes
        toastElement.classList.remove("bg-success", "bg-danger", "bg-warning", "bg-info");
        
        // Add color based on type
        switch(type) {
            case "success":
                toastElement.classList.add("bg-success", "text-white");
                break;
            case "danger":
                toastElement.classList.add("bg-danger", "text-white");
                break;
            case "warning":
                toastElement.classList.add("bg-warning", "text-dark");
                break;
            default:
                toastElement.classList.add("bg-info", "text-white");
        }
    }
    
    // Show the toast
    if (typeof scannerToast.show === 'function') {
        scannerToast.show();
    } else {
        console.error("Toast show method not found");
    }
}
    
    // Quick quantity panel functionality
    function openQuickQuantityPanel(itemId) {
        const item = scannedItems.find(item => item.id == itemId);
        if (!item) return;
        
        currentEditItemId = itemId;
        quickQtyProduct.textContent = item.name;
        quickQtyInput.value = item.quantity;
        quickQtyInput.max = item.available;
        quickQtyAvailable.textContent = item.available;
        
        quickQtyPanel.style.display = 'block';
        quickQtyInput.focus();
        quickQtyInput.select();
    }
    
    function closeQuickQuantityPanel() {
        quickQtyPanel.style.display = 'none';
        currentEditItemId = null;
        setTimeout(() => hiddenInput.focus(), 100);
    }
    
    // Quick quantity panel event listeners
    incQtyBtn.addEventListener('click', function() {
        let currentQty = parseInt(quickQtyInput.value);
        let maxQty = parseInt(quickQtyInput.max);
        
        if (currentQty < maxQty) {
            quickQtyInput.value = currentQty + 1;
        }
    });
    
    decQtyBtn.addEventListener('click', function() {
        let currentQty = parseInt(quickQtyInput.value);
        
        if (currentQty > 1) {
            quickQtyInput.value = currentQty - 1;
        }
    });
    
    confirmQuickQtyBtn.addEventListener('click', function() {
        if (!currentEditItemId) return;
        
        const index = scannedItems.findIndex(item => item.id == currentEditItemId);
        if (index !== -1) {
            const newQty = parseInt(quickQtyInput.value);
            if (!isNaN(newQty) && newQty >= 1 && newQty <= scannedItems[index].available) {
                scannedItems[index].quantity = newQty;
                updateItemRow(index);
                updateTotals();
                closeQuickQuantityPanel();
            }
        }
    });
    
    closeQuickQtyBtn.addEventListener('click', closeQuickQuantityPanel);
    
    // Clear all scanned items
    clearAllBtn.addEventListener('click', function() {
        if (scannedItems.length === 0) return;
        
        if (confirm("Are you sure you want to clear all scanned items?")) {
            scannedItems = [];
            scannedItemsList.innerHTML = '';
            scannedItemsList.appendChild(noItemsRow);
            noItemsRow.style.display = '';
            checkoutBtn.disabled = true;
            updateTotals();
            showToast("Cleared", "All items have been cleared", "info");
        }
    });
    
    // Handle checkout
    checkoutBtn.addEventListener('click', function() {
        if (scannedItems.length === 0) return;
        
        updateScanFeedback("Processing items...", "info");
        checkoutBtn.disabled = true;
        
        // Prepare data for server
        const checkoutData = {
            items: scannedItems.map(item => ({
                product_id: item.id,
                quantity: item.quantity
            }))
        };
        
        console.log("Sending checkout data:", checkoutData);
        
        // Send to server
        fetch('process_product_usage_bulk.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(checkoutData)
        })
        .then(response => {
            // Debug the raw response
            return response.text().then(text => {
                console.log("Raw server response:", text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("JSON Parse Error:", e);
                    console.error("Problematic text:", text);
                    throw new Error("Invalid JSON response from server");
                }
            });
        })
        .then(data => {
            console.log("Processed data:", data);
            if (data.success) {
                updateScanFeedback("Items processed successfully!", "success");
                showToast("Success", `${data.total_items} items have been processed`, "success");
                
                // Clear items after successful checkout
                setTimeout(() => {
                    scannedItems = [];
                    scannedItemsList.innerHTML = '';
                    scannedItemsList.appendChild(noItemsRow);
                    noItemsRow.style.display = '';
                    checkoutBtn.disabled = true;
                    updateTotals();
                    
                    // Close modal after a delay
                    setTimeout(() => barcodeModal.hide(), 1500);
                }, 1000);
            } else {
                updateScanFeedback("Processing failed: " + (data.message || "Unknown error"), "error");
                checkoutBtn.disabled = false;
                showToast("Error", data.message || "Failed to process items", "danger");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            updateScanFeedback("Server error during processing", "error");
            checkoutBtn.disabled = false;
            showToast("Error", "Server error during processing", "danger");
        });
    });
    
    // Reset scanner UI
    function resetScannerUI() {
        barcodeFeedback.textContent = "Scan a product barcode to begin";
        scanningStatus.classList.remove("alert-danger", "alert-success", "scanning-error", "scanning-success");
        scanningStatus.classList.add("alert-info");
        
        // Keep existing items if any
        if (scannedItems.length === 0) {
            noItemsRow.style.display = '';
            checkoutBtn.disabled = true;
        } else {
            noItemsRow.style.display = 'none';
            checkoutBtn.disabled = false;
        }
        
        closeQuickQuantityPanel();
    }
});

function confirmAction(action, requestId) {
  let confirmMessage = '';
  let actionUrl = '';
  
  if(action === 'approve') {
    confirmMessage = 'Are you sure you want to approve this request?';
    actionUrl = 'approve_requests.php';
  } else if(action === 'deny') {
    confirmMessage = 'Are you sure you want to deny this request?';
    actionUrl = 'deny_requests.php';
  }
  
  if(confirm(confirmMessage)) {
    // Create and submit a form programmatically
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = actionUrl;
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'request_id';
    input.value = requestId;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}
</script>

<?php include_once('layouts/footer.php'); ?>



