-- ============================================
-- Inventory Management System - Database Setup
-- ============================================

CREATE DATABASE IF NOT EXISTS inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventory_db;

-- Users Table (Admin only)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image VARCHAR(255) DEFAULT NULL,
    date_added DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ============================================
-- Default Admin Account
-- Password: admin123
-- ============================================
INSERT INTO users (username, password)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE id=id;

-- ============================================
-- Sample Categories
-- ============================================
INSERT INTO categories (name) VALUES
('Electronics'),
('Office Supplies'),
('Furniture'),
('Food & Beverages'),
('Clothing')
ON DUPLICATE KEY UPDATE id=id;

-- ============================================
-- Sample Products
-- ============================================
INSERT INTO products (name, category_id, quantity, price, date_added) VALUES
('Laptop Dell XPS 13', 1, 12, 75000.00, CURDATE()),
('Wireless Mouse', 1, 3, 850.00, CURDATE()),
('USB-C Hub', 1, 8, 1500.00, CURDATE()),
('A4 Bond Paper (500 sheets)', 2, 50, 250.00, CURDATE()),
('Ballpen Set', 2, 2, 120.00, CURDATE()),
('Stapler', 2, 15, 350.00, CURDATE()),
('Office Chair', 3, 4, 8500.00, CURDATE()),
('Standing Desk', 3, 1, 22000.00, CURDATE()),
('Instant Coffee 200g', 4, 30, 180.00, CURDATE()),
('Mineral Water 24-pack', 4, 7, 300.00, CURDATE());
