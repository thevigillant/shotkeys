<?php
// Iniciar sessão em todas as páginas
session_start();

// AJUSTE ESSAS VARIÁVEIS COM OS DADOS DA SUA BASE
$DB_HOST = 'localhost';        // confirme no hPanel
$DB_NAME = 'SEU_BANCO';
$DB_USER = 'SEU_USUARIO';
$DB_PASS = 'SUA_SENHA';

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