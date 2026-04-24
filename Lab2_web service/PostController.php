<?php
// ============================================================
// src/Controllers/PostController.php
//
// This controller handles all 5 routes for the posts resource:
//
//   GET    /api/posts         → index()   (public)
//   GET    /api/posts/{id}    → show()    (public)
//   POST   /api/posts         → store()   (protected)
//   PUT    /api/posts/{id}    → update()  (protected)
//   DELETE /api/posts/{id}    → destroy() (protected)
//
// A controller's job:
//  1. Read input (URL params, request body).
//  2. Validate it.
//  3. Call the Model to do the database work.
//  4. Send a Response.
// ============================================================

namespace App\Controllers;

use App\Helpers\Response;
use App\Middleware\AuthMiddleware;
use App\Models\Post;

class PostController
{
    private Post $model;

    public function __construct()
    {
        $this->model = new Post();
    }

    // ----------------------------------------------------------
    // index() — GET /api/posts
    //
    // Public route: anyone can list posts, no token needed.
    // ----------------------------------------------------------
    public function index(): void
    {
        $posts = $this->model->getAll();

        Response::success($posts, 'Posts retrieved successfully');
    }

    // ----------------------------------------------------------
    // show() — GET /api/posts/{id}
    //
    // Public route: return a single post by ID.
    // If the ID does not exist → 404 Not Found.
    // ----------------------------------------------------------
    public function show(int $id): void
    {
        $post = $this->model->getById($id);

        if (!$post) {
            Response::error("Post with ID $id not found", 404);
        }

        Response::success($post, 'Post retrieved successfully');
    }

    // ----------------------------------------------------------
    // store() — POST /api/posts
    //
    // Protected: requires a valid JWT.
    // Creates a new post owned by the logged-in user.
    // ----------------------------------------------------------
    public function store(): void
    {
        // 1. Authenticate — AuthMiddleware reads the JWT from the
        //    Authorization header and returns the decoded payload.
        //    If the token is missing or invalid it stops execution.
        $authUser = AuthMiddleware::handle();

        // 2. Read JSON body
        $body    = json_decode(file_get_contents('php://input'), true) ?? [];
        $title   = trim($body['title']   ?? '');
        $content = trim($body['content'] ?? '');

        // 3. Validate
        $errors = $this->validatePost($title, $content);
        if (!empty($errors)) {
            Response::error('Validation Failed', 422, $errors);
        }

        // 4. Create the post (user_id comes from the JWT, not the request body —
        //    this prevents a user from creating posts as someone else)
        $post = $this->model->create([
            'title'   => $title,
            'content' => $content,
            'user_id' => $authUser->sub,  // 'sub' is the user ID we put in the JWT
        ]);

        // 5. Return 201 Created
        Response::success($post, 'Post created successfully', 201);
    }

    // ----------------------------------------------------------
    // update() — PUT /api/posts/{id}
    //
    // Protected: requires a valid JWT.
    // Only the owner of the post can update it.
    // ----------------------------------------------------------
    public function update(int $id): void
    {
        // 1. Authenticate
        $authUser = AuthMiddleware::handle();

        // 2. Check the post exists
        $post = $this->model->getById($id);
        if (!$post) {
            Response::error("Post with ID $id not found", 404);
        }

        // 3. Authorization: is this post owned by the logged-in user?
        //    (Authentication = who are you?  Authorization = what can you do?)
        if (!$this->model->belongsToUser($id, $authUser->sub)) {
            Response::error('Forbidden: You can only edit your own posts', 403);
        }

        // 4. Read and validate body
        $body    = json_decode(file_get_contents('php://input'), true) ?? [];
        $title   = trim($body['title']   ?? '');
        $content = trim($body['content'] ?? '');

        $errors = $this->validatePost($title, $content);
        if (!empty($errors)) {
            Response::error('Validation Failed', 422, $errors);
        }

        // 5. Update and return
        $updated = $this->model->update($id, compact('title', 'content'));
        Response::success($updated, 'Post updated successfully');
    }

    // ----------------------------------------------------------
    // destroy() — DELETE /api/posts/{id}
    //
    // Protected: requires a valid JWT.
    // Only the owner of the post can delete it.
    // ----------------------------------------------------------
    public function destroy(int $id): void
    {
        // 1. Authenticate
        $authUser = AuthMiddleware::handle();

        // 2. Check the post exists
        $post = $this->model->getById($id);
        if (!$post) {
            Response::error("Post with ID $id not found", 404);
        }

        // 3. Authorization check
        if (!$this->model->belongsToUser($id, $authUser->sub)) {
            Response::error('Forbidden: You can only delete your own posts', 403);
        }

        // 4. Delete
        $this->model->delete($id);

        // 5. Return null data — there's nothing left to return
        Response::success(null, 'Post deleted successfully');
    }

    // ----------------------------------------------------------
    // validatePost(): Reusable validation for title + content.
    //
    // Returns an array of error messages, empty if valid.
    // This mirrors the 422 response format from the spec.
    // ----------------------------------------------------------
    private function validatePost(string $title, string $content): array
    {
        $errors = [];

        if (empty($title)) {
            $errors['title'][] = 'The title field is required.';
        } elseif (strlen($title) < 5) {
            $errors['title'][] = 'The title must be at least 5 characters.';
        } elseif (strlen($title) > 255) {
            $errors['title'][] = 'The title must not exceed 255 characters.';
        }

        if (empty($content)) {
            $errors['content'][] = 'The content field is required.';
        } elseif (strlen($content) < 10) {
            $errors['content'][] = 'The content must be at least 10 characters.';
        }

        return $errors;
    }
}
