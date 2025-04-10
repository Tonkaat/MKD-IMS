<?php
require_once('includes/load.php');

$query = "SELECT borrowed_items.borrow_id, stock.stock_number, borrowed_items.borrower_name, borrowed_items.due_date 
          FROM borrowed_items 
          JOIN stock ON borrowed_items.borrow_id = stock.id 
          WHERE borrowed_items.stat = 'Borrowed'";

$result = $db->query($query);

if ($result->num_rows > 0) {
    echo '<ul>';
    while ($row = $result->fetch_assoc()) {
        // Check if the item is overdue
        $due_date = $row['due_date'];
        $today = date("Y-m-d");
        $overdue = (strtotime($due_date) < strtotime($today)) ? "<span style='color: red;'>(Overdue)</span>" : "";

        echo '<li>' . $row['stock_number'] . ' (Borrowed by: ' . $row['borrower_name'] . ') - Due: ' . $due_date . ' ' . $overdue . '
                <button class="returnBtn" data-id="' . $row['borrow_id'] . '">Return</button></li>';
    }
    echo '</ul>';
} else {
    echo '<p>No borrowed items.</p>';
}
?>
