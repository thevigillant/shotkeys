<?php
declare(strict_types=1);

// Sessão em todas as páginas
if (session_status() === PHP_SESSION_NONE) {
  // Cookies um pouco mais seguros (Hostinger/Apache geralmente suporta)
  ini_set('session.cookie_httponly', '1');
  ini_set('session.use_strict_mode', '1');
  // Se o site estiver em HTTPS, descomente:
  // ini_set('session.cookie_secure', '1');
  session_start();
}

// Opcional: carregar config.local.php (não versionar)
$local_config = [];
$local_config_path = __DIR__ . '/config.local.php';
if (file_exists($local_config_path)) {
  $loaded = require $local_config_path;
  if (is_array($loaded)) $local_config = $loaded;
}

// Credenciais (prioridade: env > config.local.php)
$DB_HOST = getenv('DB_HOST') ?: ($local_config['localhost'] ?? 'localhost');
$DB_NAME = getenv('DB_NAME') ?: ($local_config['dbshotkeys'] ?? '');
$DB_USER = getenv('DB_USER') ?: ($local_config['admshot'] ?? '');
$DB_PASS = getenv('DB_PASS') ?: ($local_config['@Shotkeys2026'] ?? '');

if ($DB_NAME === '' || $DB_USER === '') {
  http_response_code(500);
  exit('Configuração de banco ausente. Configure DB_NAME e DB_USER (env ou config.local.php).');
}

try {
  $pdo = new PDO(
    "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]
  );
} catch (Throwable $e) {
  // Em produção, não exponha detalhes
  error_log('DB connection error: ' . $e->getMessage());
  http_response_code(500);
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
