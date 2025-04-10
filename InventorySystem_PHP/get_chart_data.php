<?php
require_once('includes/load.php');

// Fetch borrowed and returned count per month
$query = "
    SELECT 
        MONTH(borrowed_date) AS month, 
        SUM(CASE WHEN stat = 'Borrowed' THEN 1 ELSE 0 END) AS borrowed_count,
        SUM(CASE WHEN stat = 'Returned' THEN 1 ELSE 0 END) AS returned_count
    FROM borrowed_items
    WHERE borrowed_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY MONTH(borrowed_date)
    ORDER BY MONTH(borrowed_date)
";

$result = $db->query($query);

$months = [];
$borrowed = [];
$returned = [];

while ($row = $result->fetch_assoc()) {
    $months[] = date("M", mktime(0, 0, 0, $row['month'], 1));
    $borrowed[] = $row['borrowed_count'];
    $returned[] = $row['returned_count'];
}

// Return JSON response
echo json_encode(["months" => $months, "borrowed" => $borrowed, "returned" => $returned]);
?>
