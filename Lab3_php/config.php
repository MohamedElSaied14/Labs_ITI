<?php
// config.php – PDO connection + session/cookie auth helpers

define('DB_HOST',    'localhost');
define('DB_NAME',    'tesst_db');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

define('ALLOWED_SKILLS', ['PHP', 'J2SE', 'MySQL', 'PostgreSQL']);

define('COUNTRIES', [
    'Afghanistan','Albania','Algeria','Argentina','Australia','Austria','Belgium',
    'Brazil','Canada','Chile','China','Colombia','Czech Republic','Denmark',
    'Egypt','Finland','France','Germany','Ghana','Greece','Hungary','India',
    'Indonesia','Iran','Iraq','Ireland','Israel','Italy','Japan','Jordan','Kenya',
    'Malaysia','Mexico','Morocco','Netherlands','New Zealand','Nigeria','Norway',
    'Pakistan','Peru','Philippines','Poland','Portugal','Romania','Russia',
    'Saudi Arabia','Singapore','South Africa','South Korea','Spain','Sri Lanka',
    'Sweden','Switzerland','Syria','Taiwan','Thailand','Tunisia','Turkey',
    'Ukraine','United Arab Emirates','United Kingdom','United States',
    'Vietnam','Yemen','Zimbabwe',
]);

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');
define('ALLOWED_IMG_TYPES', ['image/jpeg','image/png','image/gif','image/webp']);
define('MAX_IMG_SIZE', 2 * 1024 * 1024); // 2 MB

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    try {
        $pdo = new PDO(
            "mysql:host=".DB_HOST.";charset=".DB_CHARSET,
            DB_USER, DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `".DB_NAME."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `".DB_NAME."`");

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id            INT AUTO_INCREMENT PRIMARY KEY,
            first_name    VARCHAR(100)  NOT NULL,
            last_name     VARCHAR(100)  NOT NULL,
            address       TEXT          NOT NULL,
            country       VARCHAR(100)  NOT NULL,
            gender        ENUM('Male','Female') NOT NULL,
            skills        VARCHAR(255)  NOT NULL,
            username      VARCHAR(80)   NOT NULL UNIQUE,
            password      VARCHAR(255)  NOT NULL,
            department    VARCHAR(150)  NOT NULL DEFAULT 'OpenSource',
            email         VARCHAR(180)  NOT NULL DEFAULT '',
            profile_image VARCHAR(255)  NOT NULL DEFAULT '',
            created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
        )");

        // Upgrade: add profile_image if not exists
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NOT NULL DEFAULT ''");
        } catch (PDOException $e) { /* already exists */ }

    } catch (PDOException $e) {
        die('<div style="font-family:sans-serif;padding:30px;color:#c00">
            <h2>&#9888; Database Connection Failed</h2>
            <p><strong>'.$e->getMessage().'</strong></p>
            <p>Open <code>config.php</code> and set the correct DB_USER / DB_PASS.</p>
        </div>');
    }

    return $pdo;
}

// ── Session ───────────────────────────────────────────────────────────────────

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function loginUser(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id']       = $user['id'];
    $_SESSION['user_username'] = $user['username'];
    $token = bin2hex(random_bytes(32));
    $_SESSION['auth_token'] = $token;
    setcookie('auth_token', $token, [
        'expires'  => time() + 60 * 60 * 24 * 30,
        'path'     => '/',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function logoutUser(): void {
    $_SESSION = [];
    session_destroy();
    setcookie('auth_token', '', ['expires' => time() - 3600, 'path' => '/']);
}

function isLoggedIn(): bool {
    if (!empty($_SESSION['user_id'])) return true;
    if (!empty($_COOKIE['auth_token']) && !empty($_SESSION['auth_token']) &&
        hash_equals($_SESSION['auth_token'], $_COOKIE['auth_token'])) return true;
    return false;
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: signin.php');
        exit;
    }
}
