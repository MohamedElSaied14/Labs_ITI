<?php
// ============================================================
// config/config.php
//
// Central place for all settings.
// Change these values to match your environment.
// ============================================================

return [

    // --- Database ---
    // PDO will use these to connect to MySQL.
    'db' => [
        'host'     => 'localhost',
        'dbname'   => 'posts_api',
        'user'     => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    // --- JWT ---
    // The secret is like a password used to sign tokens.
    // Change this to a long random string in production!
    'jwt' => [
        'secret'     => 'CHANGE_THIS_TO_A_LONG_RANDOM_SECRET_KEY_IN_PRODUCTION',
        'expiry'     => 3600, // seconds → 1 hour
        'algorithm'  => 'HS256',
    ],

];
