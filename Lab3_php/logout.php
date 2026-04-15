<?php
require_once 'config.php';
logoutUser();
header('Location: signin.php');
exit;
