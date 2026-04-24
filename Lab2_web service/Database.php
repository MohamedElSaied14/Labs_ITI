<?php
// ============================================================
// src/Models/Database.php
//
// What is PDO?
// ------------
// PDO (PHP Data Objects) is a built-in PHP class that lets
// you talk to many databases (MySQL, PostgreSQL, SQLite…)
// using the same API.  It protects against SQL injection when
// you use prepared statements.
//
// What is a Singleton?
// --------------------
// We only ever want ONE database connection per request —
// opening multiple connections wastes resources.  The singleton
// pattern ensures getInstance() always returns the same object.
// ============================================================

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    // The single shared PDO instance (null until first call)
    private static ?PDO $instance = null;

    // Private constructor prevents "new Database()" from outside
    private function __construct() {}

    // ----------------------------------------------------------
    // getInstance(): Return the shared PDO connection.
    //
    // On the first call it creates the connection.
    // On every subsequent call it returns the existing one.
    // ----------------------------------------------------------
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/config.php';
            $db     = $config['db'];

            // DSN = "Data Source Name" — tells PDO how to connect
            $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";

            try {
                self::$instance = new PDO($dsn, $db['user'], $db['password'], [
                    // Throw exceptions on error (instead of silent failure)
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    // Return rows as associative arrays by default
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Disable emulated prepared statements (more secure)
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // Don't expose DB error details to the client!
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database connection failed']);
                exit();
            }
        }

        return self::$instance;
    }
}
