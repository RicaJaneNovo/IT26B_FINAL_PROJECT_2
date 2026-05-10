-- ============================================================
-- PawDiary - Database Setup
-- Import this file in phpMyAdmin to initialize the database
-- ============================================================

-- Create and select the database
CREATE DATABASE IF NOT EXISTS pawdiary_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pawdiary_db;

-- ── USERS TABLE ──
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── PETS TABLE ──
CREATE TABLE IF NOT EXISTS pets (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    name       VARCHAR(100) NOT NULL,
    species    VARCHAR(50),
    breed      VARCHAR(100),
    age        INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── ACTIVITIES TABLE ──
CREATE TABLE IF NOT EXISTS activities (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    pet_name    VARCHAR(100),
    type        VARCHAR(50),
    date        DATE,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── HEALTH RECORDS TABLE ──
CREATE TABLE IF NOT EXISTS health_records (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    pet_name   VARCHAR(100),
    type       VARCHAR(50),
    date       DATE,
    notes      TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Setup complete! All 4 tables are ready.
-- ============================================================
