<?php
require 'includes.php';

// Get the ID from the URL: delete.php?id=rec_abc123
$id = $_GET['id'] ?? '';

if ($id !== '') {
    $records = read_all();  // load all records

    // Keep every record EXCEPT the one with this ID
    $records = array_filter($records, function($r) use ($id) {
        return $r['id'] !== $id;
    });

    // array_filter keeps keys — re-index from 0 with array_values
    write_all(array_values($records));
}

// Always redirect back to the list
header('Location: data.php?deleted=1');
exit;
