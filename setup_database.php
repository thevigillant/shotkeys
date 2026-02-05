<?php
require 'config.php';

try {
    $pdo->exec("SET NAMES 'utf8mb4'");

    // 1. Users Table (Update/Create)
    echo "Checking 'users' table... ";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    // Ensure 'role' column exists if table already existed
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER password_hash");
    } catch (PDOException $e) { /* Column likely exists */ }
    echo "OK\n";

    // 2. Products Table (Update/Create)
    echo "Checking 'products' table... ";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT,
            price_cents INT NOT NULL,
            type ENUM('OWN_KEY', 'RANDOM_BOX', 'AFFILIATE') NOT NULL,
            platform VARCHAR(50) DEFAULT NULL,
            image_url VARCHAR(255) DEFAULT NULL,
            affiliate_url VARCHAR(255) DEFAULT NULL,
            active TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    // Add missing columns if needed
    try { $pdo->exec("ALTER TABLE products ADD COLUMN platform VARCHAR(50) DEFAULT NULL AFTER type"); } catch (PDOException $e) {}
    try { $pdo->exec("ALTER TABLE products ADD COLUMN active TINYINT(1) DEFAULT 1 AFTER affiliate_url"); } catch (PDOException $e) {}
    echo "OK\n";

    // 3. Product Keys Table (NEW - Critical)
    echo "Checking 'product_keys' table... ";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS product_keys (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            key_encrypted TEXT NOT NULL,
            status ENUM('available', 'sold', 'revoked') DEFAULT 'available',
            order_id INT DEFAULT NULL,
            hash_partial VARCHAR(50) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id),
            INDEX (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "OK\n";

    // 4. Orders Table (Update)
    echo "Checking 'orders' table... ";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            total_cents INT NOT NULL,
            status ENUM('PENDING', 'PAID', 'DELIVERED', 'REFUNDED') DEFAULT 'PENDING',
            payment_gateway_id VARCHAR(255) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    try { $pdo->exec("ALTER TABLE orders ADD COLUMN payment_gateway_id VARCHAR(255) DEFAULT NULL AFTER status"); } catch (PDOException $e) {}
    echo "OK\n";

    // 5. Order Items Table
    echo "Checking 'order_items' table... ";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            quantity INT DEFAULT 1,
            unit_price_cents INT NOT NULL,
            type VARCHAR(50) DEFAULT 'OWN_KEY',
            FOREIGN KEY (order_id) REFERENCES orders(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "OK\n";

    // 6. Deliveries Table (NEW - Log/Audit)
    echo "Checking 'deliveries' table... ";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS deliveries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_key_id INT NOT NULL,
            delivered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            ip_address VARCHAR(45) DEFAULT NULL,
            user_agent TEXT DEFAULT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (product_key_id) REFERENCES product_keys(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "OK\n";

    echo "Database schema aligned with Execution Document successfully.";

} catch (PDOException $e) {
    die("DB Setup Error: " . $e->getMessage());
}
