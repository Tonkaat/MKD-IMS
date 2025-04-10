<?php
// Including required files
require_once('includes/load.php');
page_require_level(1); // Check user permissions

// Check if the form is submitted
if (isset($_POST['name']) && isset($_POST['type'])) {
    $name = remove_junk($db->escape($_POST['name']));
    $type = $_POST['type']; // This can be either 'category' or 'location'

    // Validate inputs
    if (empty($name)) {
        $session->msg("d", "Name is required.");
        redirect('categorie.php', false);
    }

    // Check if the type is valid (category or location)
    if ($type === 'category') {
        // Insert the new category into the database
        $query = "INSERT INTO categories (name) VALUES ('{$name}')";
    } elseif ($type === 'location') {
        // Insert the new location into the database
        $query = "INSERT INTO location (name) VALUES ('{$name}')";
    } else {
        $session->msg("d", "Invalid type.");
        redirect('categorie.php', false);
    }

    // Execute the query
    if ($db->query($query)) {
        $session->msg("s", ucfirst($type) . " added successfully.");
        redirect('categorie.php', false);
    } else {
        $session->msg("d", "Failed to add " . $type . ".");
        redirect('categorie.php', false);
    }
} else {
    $session->msg("d", "Missing required fields.");
    redirect('categorie.php', false);
}
