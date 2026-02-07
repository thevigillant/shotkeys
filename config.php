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

// Credenciais (prioridade: env > config.local.php > Hardcoded for Hostinger)
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'u341346182_dbshotkeys';
$DB_USER = getenv('DB_USER') ?: 'u341346182_admshot';
$DB_PASS = getenv('DB_PASS') ?: '@Shotkeys2026';

// Security Keys
// ⚠️ IMPORTANT: Change this to a random 32-char string in production!
define('ENCRYPTION_KEY', 'x8z5v3k9m2j4h7g1f6d0s2a5q8w3e4r1'); // Exactly 32 chars 


if ($DB_NAME === '' || $DB_USER === '') {
  // Em localhost, sem configurar o config.local.php, isso pode acontecer.
  // Mas na Hostinger as variaveis estarao la ou no config.php
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
  // DEBUG MODE: Show exact error to fix connection or other runtime issues
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  die("<h1>Erro de Servidor (Debug)</h1><p>" . $e->getMessage() . "</p><p>Trace: " . $e->getTraceAsString() . "</p>");
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
