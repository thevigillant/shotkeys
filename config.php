<?php
// Iniciar sessão em todas as páginas
session_start();

// AJUSTE ESSAS VARIÁVEIS COM OS DADOS DA SUA BASE
codex/identify-project-error-and-next-steps-gsdxyw
// Preferencialmente configure via variáveis de ambiente ou config.local.php
$local_config = [];
$local_config_path = __DIR__ . '/config.local.php';
if (file_exists($local_config_path)) {
  $loaded = require $local_config_path;
  if (is_array($loaded)) {
    $local_config = $loaded;
  }
}

$DB_HOST = getenv('DB_HOST') ?: ($local_config['DB_HOST'] ?? 'localhost'); // confirme no hPanel
$DB_NAME = getenv('DB_NAME') ?: ($local_config['DB_NAME'] ?? '');
$DB_USER = getenv('DB_USER') ?: ($local_config['DB_USER'] ?? '');
$DB_PASS = getenv('DB_PASS') ?: ($local_config['DB_PASS'] ?? '');

if ($DB_NAME === '' || $DB_USER === '') {
  http_response_code(500);
  exit('Configuração de banco de dados ausente. Configure DB_NAME e DB_USER (env ou config.local.php).');
=======
// Preferencialmente configure via variáveis de ambiente.
$DB_HOST = getenv('DB_HOST') ?: 'localhost'; // confirme no hPanel
$DB_NAME = getenv('DB_NAME') ?: '';
$DB_USER = getenv('DB_USER') ?: '';
$DB_PASS = getenv('DB_PASS') ?: '';

if ($DB_NAME === '' || $DB_USER === '') {
  exit('Configuração de banco de dados ausente.');
 main
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
