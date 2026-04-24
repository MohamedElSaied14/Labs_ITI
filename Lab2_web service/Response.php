<?php
// ============================================================
// src/Helpers/Response.php
//
// Why do we need this?
// --------------------
// Every API response should look the same, whether it's a
// success or an error.  This class is a helper that always
// produces the standard JSON structure required by the spec:
//
//   Success:  { "success": true,  "data": {...}, "message": "..." }
//   Error:    { "success": false, "message": "...", "errors": {...} }
//
// Using a single class means we never forget to set the
// Content-Type header or accidentally return inconsistent shapes.
// ============================================================

namespace App\Helpers;

class Response
{
    // ----------------------------------------------------------
    // success(): Send a 2xx JSON response.
    //
    // $data    : the resource(s) to return (array or null)
    // $message : human-readable description
    // $code    : HTTP status code (200, 201, …)
    // ----------------------------------------------------------
    public static function success(mixed $data, string $message = 'Success', int $code = 200): void
    {
        self::send([
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ], $code);
    }

    // ----------------------------------------------------------
    // error(): Send a 4xx / 5xx JSON response.
    // ----------------------------------------------------------
    public static function error(string $message, int $code = 400, array $errors = []): void
    {
        $body = [
            'success' => false,
            'message' => $message,
        ];

        // Only include the 'errors' key when there are validation details
        if (!empty($errors)) {
            $body['errors'] = $errors;
        }

        self::send($body, $code);
    }

    // ----------------------------------------------------------
    // send(): Low-level — set headers and output JSON, then exit.
    //
    // We always exit() after sending because PHP would otherwise
    // continue running code below the response call.
    // ----------------------------------------------------------
    private static function send(array $body, int $code): void
    {
        // Tell the browser / client this is JSON
        header('Content-Type: application/json; charset=utf-8');

        // Set the HTTP status code (200, 201, 404, 422, etc.)
        http_response_code($code);

        // Convert the PHP array to a JSON string and print it
        echo json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Stop execution — nothing should run after a response is sent
        exit();
    }
}
