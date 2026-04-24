<?php
// ============================================================
// src/Controllers/AuthController.php
//
// This controller handles login.
//
// Route:  POST /api/auth/login
// Body:   { "email": "...", "password": "..." }
//
// What happens:
//  1. Read email + password from the request body.
//  2. Look up the user in the database.
//  3. Verify the password with password_verify() (bcrypt).
//  4. Build a JWT and send it back.
//
// The client must store this token (localStorage, cookie…) and
// include it in every protected request.
// ============================================================

namespace App\Controllers;

use App\Helpers\JWT;
use App\Helpers\Response;
use App\Models\Database;
use PDO;

class AuthController
{
    public function login(): void
    {
        // ---- 1. Read JSON body ----
        // file_get_contents('php://input') reads the raw HTTP request body.
        // We then decode the JSON string into a PHP array.
        $body = json_decode(file_get_contents('php://input'), true) ?? [];

        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');

        // ---- 2. Basic validation ----
        if (empty($email) || empty($password)) {
            Response::error('Validation Failed', 422, [
                'email'    => empty($email)    ? ['The email field is required.']    : [],
                'password' => empty($password) ? ['The password field is required.'] : [],
            ]);
        }

        // ---- 3. Find user by email ----
        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ---- 4. Verify password ----
        // password_verify() safely compares the plain text password
        // against the stored bcrypt hash.  It's timing-safe.
        if (!$user || !password_verify($password, $user['password'])) {
            Response::error('Invalid email or password', 401);
        }

        // ---- 5. Build JWT ----
        $config = require __DIR__ . '/../../config/config.php';
        $secret = $config['jwt']['secret'];
        $expiry = $config['jwt']['expiry'];

        $token = JWT::encode([
            'sub'  => $user['id'],    // "subject" = user ID
            'name' => $user['name'],
            'iat'  => time(),         // "issued at"
            'exp'  => time() + $expiry, // expiry timestamp
        ], $secret);

        // ---- 6. Return token ----
        Response::success([
            'token'      => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiry,
        ], 'Login successful');
    }
}
