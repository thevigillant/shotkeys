<?php
// Standalone setup script for CLI execution
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Configuração para SQLite manually (avoiding session issues from config.php)
    $dbPath = __DIR__ . '/database.sqlite';
    
    // Ensure file creation
    if (!file_exists($dbPath)) {
        touch($dbPath);
    }

    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON;");

    // Create Tables for SQLite
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT UNIQUE,
            password TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT UNIQUE,
            title TEXT,
            description TEXT,
            image_url TEXT,
            price_cents INTEGER,
            type TEXT DEFAULT 'KEY', -- 'KEY', 'RANDOM_BOX', 'AFFILIATE'
            affiliate_url TEXT,
            status TEXT DEFAULT 'ACTIVE',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            status TEXT DEFAULT 'PENDING', -- 'PENDING', 'PAID', 'DELIVERED', 'CANCELED'
            total_cents INTEGER,
            payment_gateway_id TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER,
            product_id INTEGER,
            title TEXT,
            quantity INTEGER,
            unit_price_cents INTEGER,
            type TEXT
        );

        CREATE TABLE IF NOT EXISTS product_keys (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            product_id INTEGER,
            key_encrypted TEXT,
            is_used INTEGER DEFAULT 0,
            order_id INTEGER,
            user_id INTEGER,
            used_at DATETIME
        );
    ");

    // Seed Initial Products if empty
    $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO products (slug, title, description, price_cents, type) VALUES 
            ('random-box-silver', 'Random Box Silver', 'Uma chave aleatória de jogo Steam (Tier Prata).', 499, 'RANDOM_BOX'),
            ('random-box-gold', 'Random Box Gold', 'Uma chave aleatória de jogo Steam (Tier Ouro).', 990, 'RANDOM_BOX'),
            ('gta-v-key', 'GTA V Premium Edition', 'Chave de ativação para Grand Theft Auto V.', 2990, 'KEY');
        ");
        
        // Seed some Keys
        $pdo->exec("
            INSERT INTO product_keys (product_id, key_encrypted, is_used) VALUES 
            (1, '" . base64_encode('AAAA-BBBB-CCCC-DDDD') . "', 0),
            (2, '" . base64_encode('EEEE-FFFF-GGGG-HHHH') . "', 0),
            (3, '" . base64_encode('GTA5-ROCKS-TAR1-2024') . "', 0);
        ");
    }

    echo "Banco de dados SQLite configurado com sucesso! Arquivo: database.sqlite" . PHP_EOL;

} catch (PDOException $e) {
    die("Erro ao configurar DB: " . $e->getMessage() . PHP_EOL);
}
