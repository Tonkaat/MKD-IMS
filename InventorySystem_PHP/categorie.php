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
<div class="row">
    <div class="col-md-12">
        <h2>Items in <?php echo $type_name ? ucfirst($type_name) : 'Selected Category/Location'; ?></h2>
        <!-- Search bar for filtering categories/locations -->
        <input type="text" id="search-bar" class="form-control" placeholder="Search items by name...">
    </div>
</div>


<!-- Success/Error Message Box -->
<?php if ($session->has_msg()): ?>
    <div class="alert alert-<?php echo $session->msg_type(); ?>">
        <?php echo $session->msg(); ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-3">
        <!-- Sidebar for Add Category/Location -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-plus"></span> Add Category or Location</strong>
            </div>
            <div class="panel-body">
                <form action="add_category_location.php" method="POST">
                    <div class="form-group">
                        <label for="type">Add as:</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="category">Category</option>
                            <option value="location">Location</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Add</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <!-- Categories and Locations Section -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-th"></span> Categories</strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Category Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_categories as $cat): ?>
                            <tr>
                                <td class="text-center"><?php echo count_id(); ?></td>
                                <td>
                                <a href="#" class="open-modal" data-type="category" data-id="<?php echo $cat['id']; ?>">
                                    <?php echo remove_junk(ucfirst($cat['name'])); ?>
                                </a>
                                <a href="edit_categorie.php?id=<?php echo (int)$cat['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                                  <span class="glyphicon glyphicon-edit"></span>
                                </a>
                                <a href="delete_categorie.php?id=<?php echo (int)$cat['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                                  <span class="glyphicon glyphicon-trash"></span>
                                </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-map-marker"></span> Locations</strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Location Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_locations as $loc): ?>
                            <tr>
                                <td class="text-center"><?php echo count_id(); ?></td>
                                <td>
                                <a href="#" class="open-modal" data-type="location" data-id="<?php echo $loc['id']; ?>">
                                    <?php echo remove_junk(ucfirst($loc['name'])); ?>
                                </a>
                                <a href="edit_locations.php?id=<?php echo (int)$loc['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                                  <span class="glyphicon glyphicon-edit"></span>
                                </a>
                                <a href="delete_locations.php?id=<?php echo (int)$loc['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                                  <span class="glyphicon glyphicon-trash"></span>
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

<!-- Modal for displaying items with search bar -->
<div id="itemsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Items</h4>
                <!-- Search bar inside modal -->
                <input type="text" id="modal-search-bar" class="form-control" placeholder="Search items by name...">
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
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
