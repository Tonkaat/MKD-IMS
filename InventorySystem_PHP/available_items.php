<?php
require_once('includes/load.php');


$query = "SELECT id, stock_number FROM stock WHERE status_id = 1";
$result = $db->query($query);

if ($result->num_rows > 0) {
    echo '<select name="stock_id" required>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['id'] . '">' . $row['stock_number'] . '</option>';
    }
    echo '</select>';
} else {
    echo '<p>No available items.</p>';
}
?>

