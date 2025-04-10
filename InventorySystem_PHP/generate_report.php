<?php
ob_start(); // Start output buffering

$page_title = 'Generate Report';
require_once('includes/load.php');

// Check for required page access level
page_require_level(1);

// Fetch all locations
$all_locations = find_all_assoc('location');

// Initialize PDF library
require('fpdf/fpdf.php');

// If the form is submitted
// If the form is submitted

// If the form is submitted
if (isset($_POST['generate_report'])) {
    $location_id = $_POST['location'];
    $report_data = [];
    $report_title = 'Inventory Report in All Locations'; // Default title for all locations

    // Query for data based on selected location
    if ($location_id == 'all') {
        // Fetch data from all tables
        $tables = ['stock', 'location', 'products', 'status', 'categories']; // Added status and categories tables
        foreach ($tables as $table) {
            $report_data[$table] = find_all($table);
        }
    } else {
        // Query data for each table based on the selected location
        $tables = ['stock', 'products', 'status', 'categories']; // Added status and categories tables
        foreach ($tables as $table) {
            if (in_array($table, ['stock', 'products'])) { // Only fetch by location_id for stock and products
                $report_data[$table] = find_by_location_id($table, $location_id); // Updated query function
            } else {
                $report_data[$table] = find_all($table); // Fetch all data for tables without location_id
            }
        }

        // Fetch the selected location name for the title
        $location = find_by_id('location', $location_id);
        $report_title = 'Inventory Report in ' . ($location ? ucfirst($location['name']) : 'Unknown Location');
    }

    // If no data found in all tables
    $found_data = false;
    foreach ($report_data as $table => $data) {
        if (!empty($data)) {
            $found_data = true;
            break;
        }
    }

    if (!$found_data) {
        echo 'No data found for the selected location.';
        exit; // Prevent further processing
    }

    // Create the PDF if data exists
    if ($found_data) {
        ob_end_clean(); // Clear any output that might have been sent before PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);

        // Add Title
        $pdf->Cell(200, 10, $report_title, 0, 1, 'C');
        $pdf->Ln(10);

        // Create a table header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 10, 'Stock_Id', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Product Name', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Stock Number', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Location', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Categories', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Status', 1, 1, 'C'); // End the row

        // Loop through each row of stock data and print in table rows
        foreach ($report_data['stock'] as $row) {
            if (!empty($row)) {
                // Fetch product name
                $product = find_by_id('products', $row['product_id']);
                $product_name = $product ? $product['name'] : 'N/A';

                // Fetch category of the product
                $category = find_by_id('categories', $product['categorie_id']);
                $category_name = $category ? $category['name'] : 'N/A';

                // Fetch location name
                $location = find_by_id('location', $row['location_id']);
                $location_name = $location ? $location['name'] : 'N/A';

                // Fetch status name from status table
                $status = find_by_id('status', $row['status_id']);
                $status_name = $status ? $status['name'] : 'N/A';

                // Print stock data in table format
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(40, 10, $row['id'], 1, 0, 'C');
                $pdf->Cell(40, 10, $product_name, 1, 0, 'C');
                $pdf->Cell(40, 10, $row['stock_number'], 1, 0, 'C');
                $pdf->Cell(40, 10, $location_name, 1, 0, 'C');
                $pdf->Cell(40, 10, $category_name, 1, 0, 'C');
                $pdf->Cell(40, 10, $status_name, 1, 1, 'C');
            }
        }
        // Save report generation history
        $user_id = $_SESSION['user_id']; // Assuming user is logged in and their ID is stored in session
        $location_id = $location_id == 'all' ? null : $location_id;
        $file_path = '/path/to/report/folder/report_' . time() . '.pdf'; // You can save the PDF file if needed

        if (insert_report_history($user_id, $location_id, $report_title, $file_path)) {
            // Successfully inserted report history
        }
        // Output the PDF
        $pdf->Output();
        exit; // End the script after outputting PDF

    } else {
        echo 'No data found for the selected location.';
        exit;
    }
}
?>


<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>

    <!-- Report Generation Form -->
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Generate Report</strong>
            </div>
            <div class="panel-body">
                <!-- Form for Report Generation -->
                <form method="post" action="generate_report.php">
                    <div class="form-group">
                        <label for="location">Choose Location:</label>
                        <select name="location" id="location" class="form-control">
                            <option value="all">All Locations</option>
                            <?php foreach ($all_locations as $loc): ?>
                                <option value="<?php echo $loc['id']; ?>"><?php echo remove_junk(ucfirst($loc['name'])); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" name="generate_report" class="btn btn-primary">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

