<?php
// config.php – PDO connection + auto-creates DB & table on first run
// Edit DB_USER / DB_PASS to match your MySQL credentials.

define('DB_HOST',    'localhost');
define('DB_NAME',    'tesst_db');
define('DB_USER',    'root');   // ← your MySQL username
define('DB_PASS',    '');       // ← your MySQL password (blank for XAMPP default)
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

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    try {
        // Connect WITHOUT a database first so we can create it if needed
        $pdo = new PDO(
            "mysql:host=".DB_HOST.";charset=".DB_CHARSET,
            DB_USER, DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );

        // Auto-create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `".DB_NAME."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `".DB_NAME."`");

        // Auto-create table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100)  NOT NULL,
            last_name  VARCHAR(100)  NOT NULL,
            address    TEXT          NOT NULL,
            country    VARCHAR(100)  NOT NULL,
            gender     ENUM('Male','Female') NOT NULL,
            skills     VARCHAR(255)  NOT NULL,
            username   VARCHAR(80)   NOT NULL UNIQUE,
            password   VARCHAR(255)  NOT NULL,
            department VARCHAR(150)  NOT NULL DEFAULT 'OpenSource',
            email      VARCHAR(180)  NOT NULL DEFAULT '',
            created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
        )");

    } catch (PDOException $e) {
        die('<div style="font-family:sans-serif;padding:30px;color:#c00">
            <h2>&#9888; Database Connection Failed</h2>
            <p><strong>'.$e->getMessage().'</strong></p>
            <p>Open <code>config.php</code> and set the correct <code>DB_USER</code> and <code>DB_PASS</code>.</p>
        </div>');
    }

    return $pdo;
}

if (session_status() === PHP_SESSION_NONE) session_start();
