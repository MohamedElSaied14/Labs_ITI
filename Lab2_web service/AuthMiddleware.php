<?php
// ============================================================
// src/Middleware/AuthMiddleware.php
//
// What is Middleware?
// -------------------
// Middleware is code that runs BEFORE your controller method.
// Think of it as a security checkpoint at the door of a club:
//
//   Client → [AuthMiddleware] → Controller → Response
//
// If the check fails, the middleware sends an error response
// immediately and the controller never runs.
//
// How the client sends the token:
//   Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.xxxx.yyyy
// ============================================================

namespace App\Middleware;

use App\Helpers\JWT;
use App\Helpers\Response;

class AuthMiddleware
{
    // ----------------------------------------------------------
    // handle(): Run the authentication check.
    //
    // Returns the decoded JWT payload (an object) on success,
    // so the controller can access the logged-in user's ID via
    //   $user->sub
    // ----------------------------------------------------------
    public static function handle(): object
    {
        // 1. Read the Authorization header sent by the client
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // Also check the redirect version (some Apache setups rename it)
        if (empty($authHeader)) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        }

        // 2. The header format is:  "Bearer <token>"
        //    We need to strip the "Bearer " prefix.
        if (!str_starts_with($authHeader, 'Bearer ')) {
            Response::error('Unauthorized: No token provided', 401);
        }

        $token = substr($authHeader, 7); // remove "Bearer "

        // 3. Try to verify the token
        try {
            $config  = require __DIR__ . '/../../config/config.php';
            $secret  = $config['jwt']['secret'];
            $payload = JWT::decode($token, $secret);
        } catch (\Exception $e) {
            // Token is missing, expired, or was tampered with
            Response::error('Unauthorized: ' . $e->getMessage(), 401);
        }

        // 4. Return the payload so the controller knows who is logged in
        return $payload;
    }
}
