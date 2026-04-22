<?php
// update.php  –  Kept for backward compatibility; updates now handled in edit.php
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
header('Location: edit.php?id=' . ($id ?: ''));
exit;
