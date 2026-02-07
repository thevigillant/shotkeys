<?php
// Standalone setup script for the browser
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

    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Instalação Concluída | ShotKeys</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>body { background: #111; color: white; }</style>
    </head>
    <body class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="text-center p-5 rounded border border-secondary bg-dark">
            <h1 class="text-success mb-4 display-4">✅ Sucesso!</h1>
            <p class="lead mb-4">O banco de dados SQLite foi configurado corretamente.</p>
            <p class="text-white-50 small mb-4">Arquivo: <code><?= htmlspecialchars($dbPath) ?></code></p>
            <a href="index.php" class="btn btn-primary btn-lg px-5 fw-bold text-uppercase">Ir para a Loja</a>
        </div>
    </body>
    </html>
    <?php

} catch (PDOException $e) {
    die("<div style='color:red; panding:20px; border:1px solid red;'><h1>Erro Fatal</h1>" . htmlspecialchars($e->getMessage()) . "</div>");
}
