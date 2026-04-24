<?php
// ============================================================
// public/index.php  —  THE FRONT CONTROLLER
//
// Every single HTTP request enters the application HERE.
// This file does three things:
//
//   1. AUTOLOADING  : Automatically load any class file when
//                     it's first used (no manual require loops).
//
//   2. CORS HEADERS : Let browsers on different domains call
//                     our API (required for frontend apps).
//
//   3. ROUTING      : Match the URL + HTTP method to a controller
//                     method, then call it.
//
// Why a single entry point?
// -------------------------
// Instead of having post_list.php, post_create.php, etc.,
// one file handles everything.  This makes it easy to add
// middleware, error handling, and global headers in one place.
// ============================================================

declare(strict_types=1);

// ---- 1. Autoloader ----
// PHP needs to know where to find your classes.
// This closure is registered with spl_autoload_register().
// Whenever you write "new App\Controllers\PostController()",
// PHP converts App\Controllers\PostController to a file path:
//   App\Controllers\PostController → ../src/Controllers/PostController.php
spl_autoload_register(function (string $class): void {
    // Convert namespace separator \ to directory separator /
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    // Our root namespace is "App", which maps to the "src" folder
    $relativePath = str_replace('App' . DIRECTORY_SEPARATOR, 'src' . DIRECTORY_SEPARATOR, $relativePath);

    $file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $relativePath . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// ---- 2. CORS Headers ----
// Cross-Origin Resource Sharing (CORS) allows web browsers to
// call your API from a different domain (e.g., a React app on
// localhost:3000 calling your API on localhost:8000).
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle "preflight" requests: before making a real request,
// browsers send an OPTIONS request to check if CORS is allowed.
// We just respond 200 OK and stop.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ---- 3. Router ----
// We parse the URL path and HTTP method, then decide which
// controller method to call.

// $_SERVER['REQUEST_URI'] might be "/api/posts/5?foo=bar"
// We strip the query string to get "/api/posts/5"
$uri    = strtok($_SERVER['REQUEST_URI'], '?');
$method = $_SERVER['REQUEST_METHOD']; // GET, POST, PUT, DELETE

// Remove a leading base path if the app is not at the root.
// If your app lives at /api_lab/, change this to '/api_lab'.
$basePath = '';
$uri = substr($uri, strlen($basePath)) ?: '/';

// ---- Route matching ----
// We use a simple if/elseif chain.
// For larger apps you would build a proper Router class,
// but this is clear and easy to understand.

use App\Controllers\PostController;
use App\Controllers\AuthController;
use App\Helpers\Response;

// --- Auth routes ---

// POST /api/auth/login  → login and get a JWT
if ($method === 'POST' && $uri === '/api/auth/login') {
    (new AuthController())->login();

// --- Post routes (public) ---

// GET /api/posts  → list all posts
} elseif ($method === 'GET' && $uri === '/api/posts') {
    (new PostController())->index();

// GET /api/posts/{id}  → single post
// We use a regex to extract the numeric ID from the URL.
// preg_match() returns 1 on match, and fills $matches array.
} elseif ($method === 'GET' && preg_match('#^/api/posts/(\d+)$#', $uri, $matches)) {
    $id = (int) $matches[1];
    (new PostController())->show($id);

// --- Post routes (protected — require JWT) ---

// POST /api/posts  → create a post
} elseif ($method === 'POST' && $uri === '/api/posts') {
    (new PostController())->store();

// PUT /api/posts/{id}  → update a post
} elseif ($method === 'PUT' && preg_match('#^/api/posts/(\d+)$#', $uri, $matches)) {
    $id = (int) $matches[1];
    (new PostController())->update($id);

// DELETE /api/posts/{id}  → delete a post
} elseif ($method === 'DELETE' && preg_match('#^/api/posts/(\d+)$#', $uri, $matches)) {
    $id = (int) $matches[1];
    (new PostController())->destroy($id);

// --- 404 fallback ---
// Nothing matched — the route doesn't exist.
} else {
    Response::error("Route not found: $method $uri", 404);
}
