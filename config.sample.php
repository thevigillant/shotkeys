<?php
// Iniciar sessão em todas as páginas
session_start();

// Copie este arquivo para config.php e ajuste com seus dados.
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'shotkeys_db';
$DB_USER = getenv('DB_USER') ?: 'shotkeys_user';
$DB_PASS = getenv('DB_PASS') ?: 'sua_senha_aqui';

if ($DB_NAME === '' || $DB_USER === '') {
  exit('Configuração de banco de dados ausente.');
}

try {
  $pdo = new PDO(
    "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  // Em produção, não exiba detalhes do erro
  exit('Erro ao conectar ao banco de dados.');
}

function is_logged_in(): bool {
  return isset($_SESSION['user_id']);
}

function require_login(): void {
  if (!is_logged_in()) {
    $redirect = urlencode($_SERVER['REQUEST_URI'] ?? 'dashboard.php');
    header("Location: login.php?redirect={$redirect}");
    exit;
  }
}
