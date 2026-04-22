-- =====================================================
-- Run this once to set up the database
-- mysql -u root -p < database.sql
-- =====================================================

CREATE DATABASE IF NOT EXISTS tesst_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tesst_db;

CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(100)  NOT NULL,
    last_name     VARCHAR(100)  NOT NULL,
    address       TEXT          NOT NULL,
    country       VARCHAR(100)  NOT NULL,
    gender        ENUM('Male','Female') NOT NULL,
    skills        VARCHAR(255)  NOT NULL COMMENT 'comma-separated: PHP,J2SE,MySQL,PostgreSQL',
    username      VARCHAR(80)   NOT NULL UNIQUE,
    password      VARCHAR(255)  NOT NULL COMMENT 'bcrypt hash',
    department    VARCHAR(150)  NOT NULL DEFAULT 'OpenSource',
    email         VARCHAR(180)  NOT NULL DEFAULT '',
    profile_image VARCHAR(255)  NOT NULL DEFAULT '' COMMENT 'filename inside /uploads/ folder',
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- Upgrade existing installs
ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255) NOT NULL DEFAULT '';
