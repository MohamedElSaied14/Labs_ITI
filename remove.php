<?php
// remove.php  –  Delete a user by ID (safe, PDO prepared statement)
require_once 'config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = getDB()->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: view.php');
exit;
