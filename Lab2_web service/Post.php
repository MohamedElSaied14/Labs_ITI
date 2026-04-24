<?php
// ============================================================
// src/Models/Post.php
//
// The "Model" is the layer that talks to the database.
// It contains all the SQL queries related to the posts table.
//
// Why separate the SQL from the controller?
// -----------------------------------------
// Keeping SQL in the model means:
//  - Controllers stay short and readable (they just call methods).
//  - If the DB schema changes, you only edit one file.
//  - You can reuse the same query in multiple controllers.
// ============================================================

namespace App\Models;

use PDO;

class Post
{
    private PDO $db;

    public function __construct()
    {
        // Get the shared database connection
        $this->db = Database::getInstance();
    }

    // ----------------------------------------------------------
    // getAll(): SELECT all posts, newest first.
    // ----------------------------------------------------------
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM posts ORDER BY created_at DESC');
        return $stmt->fetchAll();   // Returns an array of rows
    }

    // ----------------------------------------------------------
    // getById(): SELECT one post by its primary key.
    //
    // Returns the row array, or false if not found.
    // We use a prepared statement with :id placeholder —
    // this is how we prevent SQL injection.
    // ----------------------------------------------------------
    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();   // false if no row found
    }

    // ----------------------------------------------------------
    // create(): INSERT a new post and return it.
    //
    // $data must have: title, content, user_id
    // ----------------------------------------------------------
    public function create(array $data): array
    {
        $sql  = 'INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title'   => $data['title'],
            ':content' => $data['content'],
            ':user_id' => $data['user_id'],
        ]);

        // lastInsertId() returns the auto-generated ID for the new row
        $newId = (int) $this->db->lastInsertId();
        return $this->getById($newId);
    }

    // ----------------------------------------------------------
    // update(): UPDATE an existing post's title and/or content.
    //
    // Returns the updated post array.
    // ----------------------------------------------------------
    public function update(int $id, array $data): array
    {
        $sql  = 'UPDATE posts SET title = :title, content = :content WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title'   => $data['title'],
            ':content' => $data['content'],
            ':id'      => $id,
        ]);

        return $this->getById($id);
    }

    // ----------------------------------------------------------
    // delete(): DELETE a post by ID.
    //
    // Returns number of affected rows (1 = deleted, 0 = not found).
    // ----------------------------------------------------------
    public function delete(int $id): int
    {
        $stmt = $this->db->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    // ----------------------------------------------------------
    // belongsToUser(): Check if a post was written by a specific user.
    //
    // Used to prevent user A from deleting/editing user B's post.
    // ----------------------------------------------------------
    public function belongsToUser(int $postId, int $userId): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM posts WHERE id = :id AND user_id = :user_id');
        $stmt->execute([':id' => $postId, ':user_id' => $userId]);
        return (bool) $stmt->fetch();
    }
}
