<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config.php';

echo "<h1>Atualizando Banco de Dados...</h1>";

try {
    // 1. Check if 'category' exists
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'category'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE products ADD COLUMN category VARCHAR(50) DEFAULT 'Geral'");
        echo "<p style='color: green'>✅ Coluna 'category' adicionada.</p>";
    } else {
        echo "<p style='color: blue'>ℹ️ Coluna 'category' já existe.</p>";
    }

    // 2. Check if 'image_url' exists
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'image_url'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE products ADD COLUMN image_url VARCHAR(255) DEFAULT NULL");
        echo "<p style='color: green'>✅ Coluna 'image_url' adicionada.</p>";
    } else {
        echo "<p style='color: blue'>ℹ️ Coluna 'image_url' já existe.</p>";
    }
    
    // 3. Check if 'affiliate_url' exists
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'affiliate_url'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE products ADD COLUMN affiliate_url VARCHAR(255) DEFAULT NULL");
        echo "<p style='color: green'>✅ Coluna 'affiliate_url' adicionada.</p>";
    } else {
        echo "<p style='color: blue'>ℹ️ Coluna 'affiliate_url' já existe.</p>";
    }

    echo "<hr><h3>Tudo pronto! Volte para o Admin e tente salvar novamente.</h3>";
    echo "<a href='../admin/products.php'>Ir para Admin</a>";

} catch (PDOException $e) {
    die("Erro ao atualizar tabela: " . $e->getMessage());
}
