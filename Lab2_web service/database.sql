-- ============================================================
-- database.sql
-- Run this file once to create the database and tables.
-- ============================================================

CREATE DATABASE IF NOT EXISTS posts_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE posts_api;

-- -------------------------------------------------------
-- users table: stores the people who can login and post
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,          -- bcrypt hash
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- posts table: the main resource we are managing
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255)  NOT NULL,
    content    TEXT          NOT NULL,
    user_id    INT           NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- -------------------------------------------------------
-- Seed data: one demo user and two demo posts
-- Password is:  secret123
-- -------------------------------------------------------
INSERT INTO users (name, email, password) VALUES
('Alice', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO posts (title, content, user_id) VALUES
('Hello World',       'This is the first post content.',  1),
('Learning REST APIs','REST stands for Representational State Transfer.', 1);
