<?php
$host = 'srv812.hstgr.io'; // ou o host que aparecer pra você
$dbname = 'u341346182_DadosShotKeys';
$username = 'u341346182_shotadmin';
$password = 'Admshot2k25';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão bem-sucedida!";
} catch (PDOException $e) {
    echo "❌ Erro ao conectar ao banco: " . $e->getMessage();
}
