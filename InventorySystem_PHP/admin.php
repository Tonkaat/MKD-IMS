<?php
  $page_title = 'Admin Dashboard';
  require_once('includes/load.php');
  page_require_level(1);
?>
<?php include_once('layouts/header.php'); ?>
<?php $stockData = getStockBreakdown(); ?>

<div class="welcome">
  <h1>WELCOME, <?php echo remove_junk(ucfirst($user['name'])); ?>! </h1>
</div>


<!-- Notification Section -->
<div class="row">
<!-- Panel for Missing/Lost Items -->
    <div class="col-md-3">
        <div class="panel panel-box clearfix" id="missingLostCount" data-toggle="modal" data-target="#missingLostModal" style="cursor: pointer;">
            <div class="panel-icon pull-left bg-green">
                <i class="glyphicon glyphicon-eye-close"></i>
            </div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"><?php echo count_missinglost_items(); ?></h2>
                <p class="text-muted">Missing/Lost Items</p>
            </div>
        </div>
    </div>

        <!-- Modal Structure for Missing/Lost Items -->
    <div id="missingLostModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Missing/Lost Items</h4>
                </div>
                <div class="modal-body">
                    <ul>
                        <?php
                        // Fetch and display the missing and lost items here
                        $missing_lost_items = get_missing_lost_items(); // This function should return an array of items
                        if (!empty($missing_lost_items)) {
                            foreach ($missing_lost_items as $item) {
                                echo "<li>" . htmlspecialchars($item['stock_number']) . "</li>"; // Adjust based on your data structure
                            }
                        } else {
                            echo "<li>No missing/lost items.</li>";
                        }
                        ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-box clearfix" data-toggle="modal" data-target="#recentActionsModal" style="cursor: pointer;">
            <div class="panel-icon pull-left bg-blue">
                <i class="glyphicon glyphicon-refresh"></i>
            </div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"></h2>
                <p class="text-muted">Recent Admin Actions</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-yellow">
                <i class="glyphicon glyphicon-book"></i>
            </div>
            <div class="panel-value pull-right">
                <h2 class="margin-top"><?php echo count_borrowed_items(); ?></h2>
                <p class="text-muted">Currently Borrowed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-box clearfix">
            <div class="panel-icon pull-left bg-red">
                <i class="glyphicon glyphicon-warning-sign"></i>
            </div>
            <div class="panel-value pull-right">
                <h2 class="margin-top">1</h2>
                <p class="text-muted">Low Stock Items</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Scan (Barcode) -->
<div class="row">
    <div class="col-md-12 text-center">
        <button class="btn btn-primary btn-lg" id="scanBtn">Scan Barcode</button>
        <button class="btn btn-primary btn-lg" id="borrowBtn">Borrow Item</button>
        <button class="btn btn-primary btn-lg" id="returnBtn">Return Item</button>
    </div>
</div>
<!-- Borrow Modal -->
<div id="borrowModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Borrow Item</h2>
        <form id="borrowForm" method="POST" action="borrow_product.php">
            <label for="borrower_name">Borrower Name:</label>
            <input type="text" name="borrower_name" required>

            <label for="stock_id">Select Item:</label>
            <div id="availableItems">
                <!-- Available items will be populated here using JavaScript -->
            </div>

            <label for="due_date">Due Date:</label>
            <input type="date" name="due_date" required>

            <button type="submit">Borrow</button>
        </form>
    </div>
</div>
                        

<!-- Return Modal -->
<div id="returnModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Active</h2>
        <div id="borrowedItems"></div>
    </div>
</div>


<!-- Recent Actions Modal -->
<div class="modal fade" id="recentActionsModal" tabindex="-1" role="dialog" aria-labelledby="recentActionsLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="recentActionsLabel">Recent Actions</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
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
        </div>
    </div>
</div>

<!-- Control Panel (Quick Actions) -->
<div class="row">
    <div class="col-md-12 text-center">
    <a href="add_product.php"><button class="btn btn-success">Add Item</button></a>
    <a href="generate_report.php"><button class="btn btn-info">Generate Report</button></a>
    <a href="users.php"><button class="btn btn-warning">Manage Users</button></a>
    </div>
</div>


<!-- Control Section -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-user"></span>
                    <span>Currently Logged In Users</span>
                </strong>
            </div>
            <div class="panel-body">
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
        <div class="panel panel-default">
            <div class="panel-heading">
            <strong>
                <span class="glyphicon glyphicon-send"></span>
                <span>User Item Requests</span>
            </strong>
            </div>
            <div class="panel-body">
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
                    <?php
                    $requests = $db->query("
                    SELECT r.*, u.name AS user_name, r.categorie_id 
                    FROM item_requests r 
                    LEFT JOIN users u ON r.user_id = u.id 
                    ORDER BY r.request_date DESC
                    ");
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
                        <button class="btn btn-success btn-xs" onclick="confirmAction('approve', <?php echo $req['id']; ?>)">Approve</button>
                        <button class="btn btn-danger btn-xs" onclick="confirmAction('deny', <?php echo $req['id']; ?>)">Deny</button>
                        <?php elseif ($req['status'] === 'Approved'): ?>
                        <form method="POST" action="request_available.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to make this item available?');">
                            <input type="hidden" name="request_id" value="<?= $req['id']; ?>">
                            <input type="hidden" name="product-title" value="<?= remove_junk($req['item_name']); ?>">
                            <input type="hidden" name="product-categorie" value="<?= (int)$req['categorie_id']; ?>"> <!-- Fetch and pass the category ID -->
                            <input type="hidden" name="product-quantity" value="<?= (int)$req['quantity']; ?>">
                            <input type="hidden" name="product-location" value="">
                            <input type="hidden" name="product-photo" value="0">
                            <button type="submit" name="make_available" class="btn btn-primary btn-xs">Make Available</button>
                        </form>
                        <?php elseif ($req['status'] == 'Added'): ?>
                            <span class="label label-success">Item Added</span>
                        <?php else: ?>            
                        <span class="label label-default"><?php echo $req['status']; ?></span>
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

<!-- Inventory Insights (Charts) -->
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Inventory Trends</strong>
            </div>
            <div class="panel-body">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Category Breakdown</strong>
            </div>
            <div class="panel-body">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>


<!-- Chart.js for Graphs -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Line Chart (Borrowed vs. Returned)
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
                            label: "Borrowed",
                            data: data.borrowed,
                            borderColor: "blue",
                            fill: false
                        }, {
                            label: "Returned",
                            data: data.returned,
                            borderColor: "green",
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

    document.addEventListener("DOMContentLoaded", function () {
    // Borrow Item Button Click
    document.getElementById("borrowBtn").addEventListener("click", function () {
        document.getElementById("borrowModal").style.display = "block";
        loadAvailableItems();
    });

    // Return Item Button Click
    document.getElementById("returnBtn").addEventListener("click", function () {
        document.getElementById("returnModal").style.display = "block";
        loadBorrowedItems();
    });

    // Close Modals
    document.querySelectorAll(".close").forEach((closeBtn) => {
        closeBtn.addEventListener("click", function () {
            document.getElementById("borrowModal").style.display = "none";
            document.getElementById("returnModal").style.display = "none";
        });
    });

    // Function to Load Available Items
    function loadAvailableItems() {
        fetch("available_items.php")
            .then((response) => response.text())
            .then((data) => {
                document.getElementById("availableItems").innerHTML = data;
            });
    }

    // Function to Load Borrowed Items
    function loadBorrowedItems() {
        fetch("borrowed_items.php")
            .then((response) => response.text())
            .then((data) => {
                document.getElementById("borrowedItems").innerHTML = data;
            });
    }

    // Borrow Form Submission
    document.getElementById("borrowForm").addEventListener("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("borrow_product.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.text())
            .then((data) => {
                alert(data);
                document.getElementById("borrowModal").style.display = "none";
                loadAvailableItems();
            });
    });

    // Return Item Function
    function returnItem(borrowId) {
        if (confirm("Are you sure you want to return this item?")) {
            fetch("return_product.php", {
                method: "POST",
                body: JSON.stringify({borrow_id: borrowId}),
                headers: {"Content-Type": "application/json"},
            })
                .then((response) => response.text())
                .then((data) => {
                    alert(data);
                    document.getElementById("returnModal").style.display = "none";
                    loadBorrowedItems();
                });
        }
    }

    // Attach Return Function to Dynamic Elements
    document.addEventListener("click", function (e) {
        // Check if the clicked element is a "returnBtn" and trigger the returnItem function
        if (e.target && e.target.classList.contains("returnBtn")) {
            returnItem(e.target.dataset.id);
        }
    });

});

function confirmAction(action, requestId) {
  const confirmMsg = action === 'approve' ? 
    'Are you sure you want to approve this request?' : 
    'Are you sure you want to deny this request?';

  if (confirm(confirmMsg)) {
    const targetFile = action === 'approve' ? 'approve_requests.php' : 'deny_requests.php';
    window.location.href = `${targetFile}?id=${requestId}`;
  }
}

</script>
