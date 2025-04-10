<?php
require_once('includes/load.php');

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = (int)$_POST['id'];
    $type = $_POST['type'];

    if ($type === "category") {
    $sql = "SELECT s.id, s.stock_number, st.name AS status, p.name AS product, l.name AS location
            FROM stock s
            LEFT JOIN products p ON p.id = s.product_id
            LEFT JOIN categories c ON c.id = p.categorie_id
            LEFT JOIN location l ON l.id = s.location_id
            LEFT JOIN status st ON st.id = s.status_id  -- Add this join for the status name
            WHERE p.categorie_id = {$id}";
    } elseif ($type === "location") {
        $sql = "SELECT s.id, s.stock_number, st.name AS status, p.name AS product, l.name AS location
                FROM stock s
                LEFT JOIN products p ON p.id = s.product_id
                LEFT JOIN location l ON l.id = s.location_id
                LEFT JOIN status st ON st.id = s.status_id  -- Add this join for the status name
                WHERE s.location_id = {$id}";
    } else {
        echo "<tr><td colspan='5' class='text-center'>Invalid request.</td></tr>";
        exit();
    }

    $stocks = find_by_sql($sql);

    if ($stocks) {
        foreach ($stocks as $stock) {
            echo "<tr>
                    <td>{$stock['id']}</td>
                    <td>{$stock['stock_number']}</td>
                    <td>{$stock['product']}</td>
                    <td>{$stock['location']}</td>
                    <td>{$stock['status']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='5' class='text-center'>No stock found.</td></tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>Invalid request.</td></tr>";
}
?>
